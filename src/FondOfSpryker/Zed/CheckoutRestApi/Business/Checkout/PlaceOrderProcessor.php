<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

use ArrayObject;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Glue\CheckoutRestApi\CheckoutRestApiConfig;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessor as SprykerPlaceOrderProcessor;
use Generated\Shared\Transfer\RestCheckoutResponseTransfer;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCalculationFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCartFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCheckoutFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToQuoteFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Business\Checkout\Quote\QuoteReaderInterface;

class PlaceOrderProcessor extends SprykerPlaceOrderProcessor implements PlaceOrderProcessorInterface
{
    /**
     * @var \FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDateInterface
     */
    protected $quoteCreatorByDeliveryDate;

    /**
     * @var \Generated\Shared\Transfer\RestCheckoutResponseTransfer[]
     */
    protected $validationErrorResponses = [];

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer[]
     */
    protected $readyToPlaceOrder = [];

    /**
     * @var \Generated\Shared\Transfer\CheckoutResponseTransfer[]
     */
    protected $checkoutResponseTransfers = [];

    /**
     * @var \Generated\Shared\Transfer\RestCheckoutResponseTransfer[]
     */
    protected $restErrorCheckoutResponseTransfers = [];


    /**
     * @var \Generated\Shared\Transfer\RestCheckoutResponseTransfer[]|ArrayObject
     */
    protected $invalidChildQuoteRestCheckoutResponseTransfers;

    /**
     * @param \Spryker\Zed\CheckoutRestApi\Business\Checkout\Quote\QuoteReaderInterface $quoteReader
     * @param \Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCartFacadeInterface $cartFacade
     * @param \Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCheckoutFacadeInterface $checkoutFacade
     * @param \Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToQuoteFacadeInterface $quoteFacade
     * @param \Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCalculationFacadeInterface $calculationFacade
     * @param \Spryker\Zed\CheckoutRestApiExtension\Dependency\Plugin\QuoteMapperPluginInterface[] $quoteMapperPlugins
     * @param \FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDateInterface $quoteCreatorByDeliveryDate
     */
    public function __construct(
        QuoteReaderInterface $quoteReader,
        CheckoutRestApiToCartFacadeInterface $cartFacade,
        CheckoutRestApiToCheckoutFacadeInterface $checkoutFacade,
        CheckoutRestApiToQuoteFacadeInterface $quoteFacade,
        CheckoutRestApiToCalculationFacadeInterface $calculationFacade,
        array $quoteMapperPlugins,
        QuoteCreatorByDeliveryDateInterface $quoteCreatorByDeliveryDate

    ) {
        parent::__construct($quoteReader, $cartFacade, $checkoutFacade, $quoteFacade, $calculationFacade, $quoteMapperPlugins);
        $this->quoteReader = $quoteReader;
        $this->cartFacade = $cartFacade;
        $this->checkoutFacade = $checkoutFacade;
        $this->quoteFacade = $quoteFacade;
        $this->calculationFacade = $calculationFacade;
        $this->quoteMapperPlugins = $quoteMapperPlugins;
        $this->quoteCreatorByDeliveryDate = $quoteCreatorByDeliveryDate;

        $this->invalidChildQuoteRestCheckoutResponseTransfers = new ArrayObject();
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutResponseTransfer
    {
        $originalQuoteTransfer = $this->quoteReader->findCustomerQuoteByUuid($restCheckoutRequestAttributesTransfer);

        $originalRestCheckoutResponseTransfer = $this->validateQuoteTransfer($originalQuoteTransfer);
        if ($originalRestCheckoutResponseTransfer !== null || $originalQuoteTransfer === null) {
            return $originalRestCheckoutResponseTransfer;
        }

        $originalQuoteTransfer = $this->prepareQuoteTransfer($restCheckoutRequestAttributesTransfer, $originalQuoteTransfer);

        // split original, create and persist child quotes.
        $invalidatedQuoteCollectionTransfer = $this->quoteCreatorByDeliveryDate->createAndPersistChildQuotesByDeliveryDate(
            $originalQuoteTransfer
        );

        // validate child quotes and return error collection in that case.
        $validatedQuoteTransferCollection = $this->filterInvalidQuoteTransfers($invalidatedQuoteCollectionTransfer);
        if ($this->hasInvalidChildQuoteCheckoutResponseTransfer()) {
            return $this->createMultipleRestCheckoutResponseTransfer();
        }

        // Run mapper plugins and recalculate child quotes
        $validatedQuoteTransferCollection = $this->prepareQuoteCollectionTransfer(
            $restCheckoutRequestAttributesTransfer, $validatedQuoteTransferCollection
        );

        // place order
        $this->placeOrderForChildQuoteCollectionTransfer($validatedQuoteTransferCollection);


        foreach ($this->readyToPlaceOrder as $placeOrderReadyQuote) {
            $this->checkoutResponseTransfers[] = $this->executePlaceOrder($placeOrderReadyQuote);
        }

        foreach ($this->checkoutResponseTransfers as $checkoutResponseTransfer) {
            if (!$checkoutResponseTransfer->getIsSuccess()) {
                $this->restErrorCheckoutResponseTransfers[] = $this->createPlaceOrderErrorResponse($checkoutResponseTransfer);
            }
        }

        if (\count($this->restErrorCheckoutResponseTransfers) === 0) {
            $originalQuoteResponseTransfer = $this->deleteQuote($originalQuoteTransfer);
            if (!$originalQuoteResponseTransfer->getIsSuccessful()) {
                return $this->createQuoteResponseError(
                    $originalQuoteResponseTransfer,
                    CheckoutRestApiConfig::RESPONSE_CODE_UNABLE_TO_DELETE_CART,
                    CheckoutRestApiConfig::RESPONSE_DETAILS_UNABLE_TO_DELETE_CART
                );
            }
        }

        return (new RestCheckoutResponseTransfer());
    }


    protected function addSuccessfulPlaceOrderChildQuoteCheckoutResponseTransfer(): void
    {

    }

    protected function placeOrderForChildQuoteCollectionTransfer(QuoteCollectionTransfer $quoteCollectionTransfer)
    {
        foreach ($this->readyToPlaceOrder as $placeOrderReadyQuote) {
            $this->checkoutResponseTransfers[] = $this->executePlaceOrder($placeOrderReadyQuote);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     * @param \Generated\Shared\Transfer\QuoteCollectionTransfer $quoteCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    protected function prepareQuoteCollectionTransfer(
        RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer,
        QuoteCollectionTransfer $quoteCollectionTransfer
    ): QuoteCollectionTransfer {
        $quoteTransfers = new ArrayObject();
        foreach ($quoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            $quoteTransfer =  $this->prepareQuoteTransfer($restCheckoutRequestAttributesTransfer, $quoteTransfer);
            $quoteTransfers->append($quoteTransfer);
        }

        $quoteCollectionTransfer->setQuotes($quoteTransfers);

        return $quoteCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function prepareQuoteTransfer(
        RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer,
        QuoteTransfer $quoteTransfer
    ): QuoteTransfer {
        $quoteTransfer = $this->mapRestCheckoutRequestAttributesToQuote($restCheckoutRequestAttributesTransfer, $quoteTransfer);
        $quoteTransfer = $this->recalculateQuote($quoteTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteCollectionTransfer $collectionTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    protected function filterInvalidQuoteTransfers(QuoteCollectionTransfer $collectionTransfer): QuoteCollectionTransfer
    {
        $validQuoteCollectionTransfer = new QuoteCollectionTransfer();
        foreach ($collectionTransfer->getQuotes() as $quoteTransfer) {
            if ($this->isChildQuoteTransferValid($quoteTransfer)) {
                $validQuoteCollectionTransfer->addQuote($quoteTransfer);
            }
        }

        return $validQuoteCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function isChildQuoteTransferValid(QuoteTransfer $quoteTransfer): bool
    {
        $restCheckoutResponseTransfer = $this->validateQuoteTransfer($quoteTransfer);

        if ($restCheckoutResponseTransfer !== null) {
            $this->addInvalidChildQuoteCheckoutResponseTransfer($restCheckoutResponseTransfer);
            return false;
        }

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutResponseTransfer $restCheckoutResponseTransfer
     *
     * @return void
     */
    protected function addInvalidChildQuoteCheckoutResponseTransfer(
        RestCheckoutResponseTransfer $restCheckoutResponseTransfer
    ): void {
        $this->invalidChildQuoteRestCheckoutResponseTransfers->append($restCheckoutResponseTransfer);
    }

    /**
     * @return bool
     */
    protected function hasInvalidChildQuoteCheckoutResponseTransfer(): bool
    {
        return $this->invalidChildQuoteRestCheckoutResponseTransfers->count() > 0;
    }
}
