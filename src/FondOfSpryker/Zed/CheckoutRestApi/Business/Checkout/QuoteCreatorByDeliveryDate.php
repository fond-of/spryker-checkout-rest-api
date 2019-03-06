<?php

declare(strict_types=1);

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Cart\CartClientInterface;
use Spryker\Client\Quote\QuoteClientInterface;
use Spryker\Zed\Quote\Business\QuoteFacadeInterface;
use ArrayObject;
use RuntimeException;

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
     * @var \Generated\Shared\Transfer\ItemTransfer[][]
     */
    protected $itemsByDeliveryDate = [];

    /**
     * @var \Spryker\Client\Quote\QuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @var \Spryker\Client\Cart\CartClientInterface
     */
    protected $cartClient;

    /**
     * @param \Spryker\Zed\Quote\Business\QuoteFacadeInterface $quoteFacade
     * @param \Spryker\Client\Quote\QuoteClientInterface $quoteClient
     * @param \Spryker\Client\Cart\CartClientInterface $cartClient
     */
    public function __construct(
        QuoteFacadeInterface $quoteFacade,
        QuoteClientInterface $quoteClient,
        CartClientInterface $cartClient
    ) {
        $this->quoteFacade = $quoteFacade;
        $this->quoteClient = $quoteClient;
        $this->cartClient = $cartClient;
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
     * @throws \Exception
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
            $quoteTransfer = $this->createQuoteTransferFromOriginalQuoteTransfer($originalQuoteTransfer);
            $quoteTransfer = $this->persistQuoteTransfer($quoteTransfer);

            foreach ($itemTransfers as $itemTransfer) {
                $quoteTransfer = $this->addItemTransferToQuote($quoteTransfer, $itemTransfer);
            }

            $this->generatedQuotes[] = $quoteTransfer;
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransferFromOriginalQuoteTransfer(QuoteTransfer $originalQuoteTransfer): QuoteTransfer
    {

        $quote = new QuoteTransfer();
        $quote->fromArray($originalQuoteTransfer->toArray());
        $quote->setIdQuote(null);
        $quote->setUuid(null);
        $quote->setItems(new ArrayObject());
        $quote->setIsDefault(false);

        return $quote;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @throws \Exception
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function persistQuoteTransfer(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $quoteResponse = $this->quoteFacade->createQuote($quoteTransfer);
        if (! $quoteResponse->getIsSuccessful()) {
            throw new RuntimeException('Could not create Quote.');
        }

        return $quoteResponse->getQuoteTransfer();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function addItemTransferToQuote(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer): QuoteTransfer
    {
        $this->quoteClient->setQuote($quoteTransfer);

        return $this->cartClient->addItem($itemTransfer);
    }
}
