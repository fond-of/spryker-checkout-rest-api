<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

use ArrayObject;
use Generated\Shared\Transfer\CheckoutResponseQuoteCollectionTransfer;
use Generated\Shared\Transfer\CheckoutResponseQuoteTransfer;
use Generated\Shared\Transfer\ItemTransfer;
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
use Spryker\Zed\Quote\Business\QuoteFacadeInterface;

class PlaceOrderProcessor extends SprykerPlaceOrderProcessor implements PlaceOrderProcessorInterface
{
    /**
     * @var \Spryker\Zed\Quote\Business\QuoteFacadeInterface
     */
    protected $quoteFacadeReal;

    /**
     * @var \FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDateInterface
     */
    protected $quoteCreatorByDeliveryDate;

    /**
     * @var \Generated\Shared\Transfer\RestCheckoutResponseTransfer[]|ArrayObject
     */
    protected $invalidRestCheckoutResponseTransfers;

    /**
     * @param \Spryker\Zed\CheckoutRestApi\Business\Checkout\Quote\QuoteReaderInterface $quoteReader
     * @param \Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCartFacadeInterface $cartFacade
     * @param \Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCheckoutFacadeInterface $checkoutFacade
     * @param \Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToQuoteFacadeInterface $quoteFacade
     * @param \Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCalculationFacadeInterface $calculationFacade
     * @param \Spryker\Zed\CheckoutRestApiExtension\Dependency\Plugin\QuoteMapperPluginInterface[] $quoteMapperPlugins
     * @param \FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDateInterface $quoteCreatorByDeliveryDate
     * @param \Spryker\Zed\Quote\Business\QuoteFacadeInterface $quoteFacadeReal
     */
    public function __construct(
        QuoteReaderInterface $quoteReader,
        CheckoutRestApiToCartFacadeInterface $cartFacade,
        CheckoutRestApiToCheckoutFacadeInterface $checkoutFacade,
        CheckoutRestApiToQuoteFacadeInterface $quoteFacade,
        CheckoutRestApiToCalculationFacadeInterface $calculationFacade,
        array $quoteMapperPlugins,
        QuoteCreatorByDeliveryDateInterface $quoteCreatorByDeliveryDate,
        QuoteFacadeInterface $quoteFacadeReal
    ) {
        parent::__construct($quoteReader, $cartFacade, $checkoutFacade, $quoteFacade, $calculationFacade, $quoteMapperPlugins);
        $this->quoteCreatorByDeliveryDate = $quoteCreatorByDeliveryDate;
        $this->invalidRestCheckoutResponseTransfers = new ArrayObject();
        $this->quoteFacadeReal = $quoteFacadeReal;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutResponseTransfer
    {
        $originalQuoteTransfer = $this->quoteReader->findCustomerQuoteByUuid($restCheckoutRequestAttributesTransfer);
        if ($originalQuoteTransfer === null || $this->isQuoteTransferValid($originalQuoteTransfer) === false) {
            return $this->createMultipleRestCheckoutResponseTransfer();
        }

        // split original, create and persist child quotes.
        $invalidatedQuoteCollectionTransfer = $this->quoteCreatorByDeliveryDate->createAndPersistChildQuotesByDeliveryDate(
            $originalQuoteTransfer = $this->prepareQuoteTransfer($restCheckoutRequestAttributesTransfer, $originalQuoteTransfer)
        );

        // validate child quotes and return error collection in that case.
        $validatedQuoteTransferCollection = $this->filterInvalidQuoteTransfers($invalidatedQuoteCollectionTransfer);
        if ($this->hasInvalidRestCheckoutResponseTransfer()) {
            return $this->createMultipleRestCheckoutResponseTransfer();
        }

        // Run mapper plugins and recalculate child quotes
        $validatedQuoteTransferCollection = $this->prepareQuoteCollectionTransfer(
            $restCheckoutRequestAttributesTransfer, $validatedQuoteTransferCollection
        );

        // place orders
        $checkoutResponseQuoteCollectionTransfer = $this->placeOrderForQuoteCollectionTransfer($validatedQuoteTransferCollection);

        // remove items from original if some place orders failed.
        if ($this->hasInvalidCheckoutResponseTransfers($checkoutResponseQuoteCollectionTransfer)) {
            $originalQuoteTransfer = $this->removeSuccessfullyPlacedOrderItemsFromOriginalQuoteTransfer(
                $checkoutResponseQuoteCollectionTransfer,
                $originalQuoteTransfer
            );

            $originalQuoteTransfer = $this->recalculateQuote($originalQuoteTransfer);
            $originalQuoteResponseTransfer = $this->quoteFacadeReal->updateQuote($originalQuoteTransfer);
            if (! $originalQuoteResponseTransfer->getIsSuccessful()) {
                throw new \RuntimeException('Could not update original quote facade');
            }
        }

        // remove all child quotes, remove original quote if all place orders were successful.
        $this->deleteQuoteCollectionTransfer($validatedQuoteTransferCollection);
        if (! $this->hasInvalidCheckoutResponseTransfers($checkoutResponseQuoteCollectionTransfer)) {
            $this->deleteQuoteTransfer($originalQuoteTransfer);
        }

        return $this->createMultipleRestCheckoutResponseTransfer();
    }

    /**
     * TODO
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    protected function createMultipleRestCheckoutResponseTransfer(): RestCheckoutResponseTransfer
    {
        return new RestCheckoutResponseTransfer();
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseQuoteCollectionTransfer $checkoutResponseQuoteCollectionTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function removeSuccessfullyPlacedOrderItemsFromOriginalQuoteTransfer(
        CheckoutResponseQuoteCollectionTransfer $checkoutResponseQuoteCollectionTransfer,
        QuoteTransfer $originalQuoteTransfer
    ): QuoteTransfer {
        foreach ($checkoutResponseQuoteCollectionTransfer->getCheckoutResponseQuotes() as $checkoutResponseQuote) {
            if ($checkoutResponseQuote->getQuoteTransfer() === null || ! $checkoutResponseQuote->getIsSuccess()) {
                continue;
            }

            $itemTransfersToRemove = $checkoutResponseQuote->getQuoteTransfer()->getItems();
            foreach ($itemTransfersToRemove as $itemTransferToRemove) {
                $originalQuoteTransfer = $this->removeItemTransferFromQuoteTransfer($originalQuoteTransfer, $itemTransferToRemove);
            }
        }

        return $originalQuoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransferToRemove
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function removeItemTransferFromQuoteTransfer(QuoteTransfer $originalQuoteTransfer, ItemTransfer $itemTransferToRemove): QuoteTransfer
    {
        $newItemTransfers = new ArrayObject();
        foreach ($originalQuoteTransfer->getItems() as $itemTransfer) {
            if ($itemTransfer->getDeliveryTime() === $itemTransferToRemove->getDeliveryTime()
                && $itemTransfer->getSku() === $itemTransferToRemove->getSku()
                && $itemTransfer->getQuantity() === $itemTransferToRemove->getQuantity()) {

                continue;
            }

            $newItemTransfers->append($itemTransfer);
        }

        $originalQuoteTransfer->setItems($newItemTransfers);

        return $originalQuoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseQuoteCollectionTransfer $checkoutResponseQuoteCollectionTransfer
     *
     * @return bool
     */
    protected function hasInvalidCheckoutResponseTransfers(CheckoutResponseQuoteCollectionTransfer $checkoutResponseQuoteCollectionTransfer): bool
    {
        foreach ($checkoutResponseQuoteCollectionTransfer->getCheckoutResponseQuotes() as $checkoutResponseQuote) {
            if (!$checkoutResponseQuote->getIsSuccess()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteCollectionTransfer $quoteCollectionTransfer
     *
     * @return void
     */
    protected function deleteQuoteCollectionTransfer(QuoteCollectionTransfer $quoteCollectionTransfer): void
    {
        foreach ($quoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            $this->deleteQuoteTransfer($quoteTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function deleteQuoteTransfer(QuoteTransfer $quoteTransfer): void
    {
        $quoteResponseTransfer = $this->deleteQuote($quoteTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            $invalidRestCheckoutResponseTransfer = $this->createQuoteResponseError(
                $quoteResponseTransfer,
                CheckoutRestApiConfig::RESPONSE_CODE_UNABLE_TO_DELETE_CART,
                CheckoutRestApiConfig::RESPONSE_DETAILS_UNABLE_TO_DELETE_CART
            );

            $this->addInvalidRestCheckoutResponseTransfer($invalidRestCheckoutResponseTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteCollectionTransfer $validatedQuoteCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseQuoteCollectionTransfer
     */
    protected function placeOrderForQuoteCollectionTransfer(QuoteCollectionTransfer $validatedQuoteCollectionTransfer): CheckoutResponseQuoteCollectionTransfer
    {
        $checkoutResponseQuoteCollectionTransfer = new CheckoutResponseQuoteCollectionTransfer();

        foreach ($validatedQuoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            $checkoutResponseTransfer = $this->executePlaceOrder($quoteTransfer);

            // create invalid rest checkout response transfers and append them to the list.
            if (!$checkoutResponseTransfer->getIsSuccess()) {
                $this->addInvalidRestCheckoutResponseTransfer(
                    $this->createPlaceOrderErrorResponse($checkoutResponseTransfer)
                );
            }

            $checkoutResponseQuoteTransfer = new CheckoutResponseQuoteTransfer();
            $checkoutResponseQuoteTransfer->setQuoteTransfer($quoteTransfer);
            $checkoutResponseQuoteTransfer->setCheckoutResponse($checkoutResponseTransfer);
            $checkoutResponseQuoteTransfer->setIsSuccess($checkoutResponseTransfer->getIsSuccess());

            $checkoutResponseQuoteCollectionTransfer->addCheckoutResponseQuote(
                $checkoutResponseQuoteTransfer
            );
        }

        return $checkoutResponseQuoteCollectionTransfer;
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
            $quoteTransfers->append(
                $this->prepareQuoteTransfer($restCheckoutRequestAttributesTransfer, $quoteTransfer)
            );
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
            if ($this->isQuoteTransferValid($quoteTransfer)) {
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
    protected function isQuoteTransferValid(QuoteTransfer $quoteTransfer): bool
    {
        $restCheckoutResponseTransfer = $this->validateQuoteTransfer($quoteTransfer);

        if ($restCheckoutResponseTransfer !== null) {
            $this->addInvalidRestCheckoutResponseTransfer($restCheckoutResponseTransfer);
            return false;
        }

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutResponseTransfer $restCheckoutResponseTransfer
     *
     * @return void
     */
    protected function addInvalidRestCheckoutResponseTransfer(RestCheckoutResponseTransfer $restCheckoutResponseTransfer): void
    {
        $this->invalidRestCheckoutResponseTransfers->append($restCheckoutResponseTransfer);
    }

    /**
     * @return bool
     */
    protected function hasInvalidRestCheckoutResponseTransfer(): bool
    {
        return $this->invalidRestCheckoutResponseTransfers->count() > 0;
    }
}
