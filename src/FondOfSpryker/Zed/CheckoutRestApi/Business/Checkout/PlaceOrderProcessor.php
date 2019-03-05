<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

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
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    public function placeOrder(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutResponseTransfer
    {
        $originalQuoteTransfer = $this->quoteReader->findCustomerQuoteByUuid($restCheckoutRequestAttributesTransfer);

        $originalRestCheckoutResponseTransfer = $this->validateQuoteTransfer($originalQuoteTransfer);
        if ($originalRestCheckoutResponseTransfer !== null || $originalQuoteTransfer === null) {
            return $originalRestCheckoutResponseTransfer;
        }

        $originalQuoteTransfer = $this->mapRestCheckoutRequestAttributesToQuote($restCheckoutRequestAttributesTransfer, $originalQuoteTransfer);
        $originalQuoteTransfer = $this->recalculateQuote($originalQuoteTransfer);

        // start split
        $this->quoteCreatorByDeliveryDate->splitAndCreateQuotesByDeliveryDate($originalQuoteTransfer);
        $createdQuotes = $this->quoteCreatorByDeliveryDate->getGeneratedQuotes();
        // $originalQuoteTransfer = $this->quoteCreatorByDeliveryDate->getOriginalQuote();

        // map, recalculate, and validate again all cloned quotes.
        foreach ($createdQuotes as $createdQuote) {
            $restCheckoutResponseTransfer = $this->validateQuoteTransfer($createdQuote);
            if ($restCheckoutResponseTransfer !== null) {
                $this->checkoutResponseTransfers[] = $restCheckoutResponseTransfer;
                continue;
            }

            $createdQuote = $this->mapRestCheckoutRequestAttributesToQuote($restCheckoutRequestAttributesTransfer, $createdQuote);
            $createdQuote = $this->recalculateQuote($createdQuote);
            $this->readyToPlaceOrder[] = $createdQuote;
        }

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

        return new RestCheckoutResponseTransfer();
    }
}
