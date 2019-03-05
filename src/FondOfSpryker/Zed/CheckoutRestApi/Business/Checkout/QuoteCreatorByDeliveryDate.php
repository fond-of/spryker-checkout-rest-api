<?php

declare(strict_types=1);

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Quote\Business\QuoteFacadeInterface;
use ArrayObject;

class QuoteCreatorByDeliveryDate implements QuoteCreatorByDeliveryDateInterface
{
    /**
     * @var \Spryker\Zed\Quote\Business\QuoteFacadeInterface
     */
    protected $quoteFacade;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer[]
     */
    protected $generatedQuotes;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer
     */
    protected $originalQuote = [];

    /**
     * @var \Generated\Shared\Transfer\ItemTransfer[]
     */
    protected $itemsByDeliveryDate = [];

    /**
     * @param \Spryker\Zed\Quote\Business\QuoteFacadeInterface $quoteFacade
     */
    public function __construct(QuoteFacadeInterface $quoteFacade)
    {
        $this->quoteFacade = $quoteFacade;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer[]
     */
    public function getGeneratedQuotes(): array
    {
        return $this->generatedQuotes;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getOriginalQuote(): QuoteTransfer
    {
        return $this->originalQuote;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return void
     */
    protected function addItem(ItemTransfer $itemTransfer): void
    {
        $this->itemsByDeliveryDate[$itemTransfer->getDeliveryTime()][] = $itemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @return void
     */
    public function splitAndCreateQuotesByDeliveryDate(QuoteTransfer $originalQuoteTransfer): void
    {
        $this->originalQuote = $originalQuoteTransfer;

        foreach ($originalQuoteTransfer->getItems() as $item) {
            $this->addItem($item);
        }

        foreach ($this->itemsByDeliveryDate as $itemTransfers) {
            $quote = $this->createQuoteFromOriginal($originalQuoteTransfer);
            $quote->setItems(new ArrayObject($itemTransfers));

            $this->generatedQuotes[] = $quote;
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteFromOriginal(QuoteTransfer $originalQuoteTransfer): QuoteTransfer
    {
        $quote = clone $originalQuoteTransfer;
        $quote->setIdQuote(null);
        $quote->setUuid(null);

        return $quote;
    }
}
