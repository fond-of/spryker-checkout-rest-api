<?php

declare(strict_types = 1);

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteActivationRequestTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use RuntimeException;
use Spryker\Zed\MultiCart\Business\MultiCartFacadeInterface;
use Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface;

class QuoteCreatorByDeliveryDate implements QuoteCreatorByDeliveryDateInterface
{
    /**
     * @var \Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface
     */
    protected $persistentCartFacade;

    /**
     * @var \Spryker\Zed\MultiCart\Business\MultiCartFacadeInterface
     */
    protected $multiCartFacade;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer[]|\ArrayObject
     */
    protected $childQuoteTransfers;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer
     */
    protected $originalQuoteTransfer;

    /**
     * @var \Generated\Shared\Transfer\ItemTransfer[][]
     */
    protected $itemTransfersByDeliveryDate = [];

    /**
     * @param \Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface $persistentCartFacade
     * @param \Spryker\Zed\MultiCart\Business\MultiCartFacadeInterface $multiCartFacade
     */
    public function __construct(
        PersistentCartFacadeInterface $persistentCartFacade,
        MultiCartFacadeInterface $multiCartFacade
    ) {
        $this->persistentCartFacade = $persistentCartFacade;
        $this->multiCartFacade = $multiCartFacade;
        $this->childQuoteTransfers = new ArrayObject();
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getOriginalQuoteTransfer(): QuoteTransfer
    {
        return $this->originalQuoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @return void
     */
    protected function setOriginalQuoteTransfer(QuoteTransfer $originalQuoteTransfer): void
    {
        $this->originalQuoteTransfer = $originalQuoteTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer[]|\ArrayObject
     */
    protected function getChildQuoteTransfers(): ArrayObject
    {
        return $this->childQuoteTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function addChildQuoteTransfer(QuoteTransfer $quoteTransfer): void
    {
        $this->childQuoteTransfers->append($quoteTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\ItemTransfer[][]
     */
    protected function getItemTransfersGroupedByDeliveryDate(): array
    {
        return $this->itemTransfersByDeliveryDate;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return void
     */
    protected function addItemTransferGroupedByDeliveryDate(ItemTransfer $itemTransfer): void
    {
        $this->itemTransfersByDeliveryDate[$itemTransfer->getConcreteDeliveryDate()][] = $itemTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    protected function createQuoteCollectionTransferWithChildQuoteTransfers(): QuoteCollectionTransfer
    {
        $quoteCollectionTransfer = new QuoteCollectionTransfer();
        $quoteCollectionTransfer->setQuotes($this->getChildQuoteTransfers());

        return $quoteCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    public function createAndPersistChildQuotesByDeliveryDate(QuoteTransfer $originalQuoteTransfer): QuoteCollectionTransfer
    {
        $this->setOriginalQuoteTransfer($originalQuoteTransfer);
        foreach ($originalQuoteTransfer->getItems() as $item) {
            $this->addItemTransferGroupedByDeliveryDate($item);
        }

        foreach ($this->getItemTransfersGroupedByDeliveryDate() as $itemTransfers) {
            $quoteTransfer = $this->createQuoteTransferFromOriginalQuoteTransfer($originalQuoteTransfer);
            $quoteTransfer = $this->persistQuoteTransfer($quoteTransfer);
            $quoteTransfer = $this->addItemTransfersToQuote($quoteTransfer, $itemTransfers);

            $this->addChildQuoteTransfer($quoteTransfer);
        }

        $this->setOriginalQuoteAsDefault($originalQuoteTransfer);

        return $this->createQuoteCollectionTransferWithChildQuoteTransfers();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @throws \RuntimeException
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function setOriginalQuoteAsDefault(QuoteTransfer $originalQuoteTransfer): QuoteTransfer
    {
        $quoteActivationRequestTransfer = new QuoteActivationRequestTransfer();
        $quoteActivationRequestTransfer->setCustomer($originalQuoteTransfer->getCustomer())
            ->setIdQuote($originalQuoteTransfer->getIdQuote());
        $quoteResponseTransfer = $this->multiCartFacade->setDefaultQuote($quoteActivationRequestTransfer);

        if ($quoteResponseTransfer->getIsSuccessful()) {
            return $quoteResponseTransfer->getQuoteTransfer();
        }

        throw new RuntimeException('Could not reset original quote to default.');
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransferFromOriginalQuoteTransfer(QuoteTransfer $originalQuoteTransfer): QuoteTransfer
    {
        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->fromArray($originalQuoteTransfer->toArray())
            ->setIdQuote(null)
            ->setUuid(null)
            ->setItems(new ArrayObject())
            ->setIsDefault(false);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @throws \RuntimeException
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function persistQuoteTransfer(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $quoteResponseTransfer = $this->persistentCartFacade->createQuote($quoteTransfer);
        if ($quoteResponseTransfer->getIsSuccessful()) {
            return $quoteResponseTransfer->getQuoteTransfer();
        }

        throw new RuntimeException('Could not create Quote.');
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @throws \RuntimeException
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function addItemTransfersToQuote(QuoteTransfer $quoteTransfer, array $itemTransfers): QuoteTransfer
    {
        $persistentCartChangeTransfer = new PersistentCartChangeTransfer();
        $persistentCartChangeTransfer->setIdQuote($quoteTransfer->getIdQuote())
            ->setCustomer($quoteTransfer->getCustomer());

        foreach ($itemTransfers as $itemTransfer) {
            $persistentCartChangeTransfer->addItem($itemTransfer);
        }

        $quoteTransferResponse = $this->persistentCartFacade->add($persistentCartChangeTransfer);
        if ($quoteTransferResponse->getIsSuccessful()) {
            return $quoteTransferResponse->getQuoteTransfer();
        }

        throw new RuntimeException('Could not add items');
    }
}
