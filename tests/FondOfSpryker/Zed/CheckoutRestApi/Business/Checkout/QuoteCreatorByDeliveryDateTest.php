<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

use ArrayObject;
use Codeception\Test\Unit;
use Exception;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\MultiCart\Business\MultiCartFacadeInterface;
use Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface;

class QuoteCreatorByDeliveryDateTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDate
     */
    protected $quoteCreatorByDeliveryDate;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface
     */
    protected $persistentCartFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\MultiCart\Business\MultiCartFacadeInterface
     */
    protected $multiCartFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\ItemTransfer
     */
    protected $itemTransferMock;

    /**
     * @var \ArrayObject
     */
    protected $itemTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteResponseTransfer
     */
    protected $quoteResponseTransferMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->persistentCartFacadeInterfaceMock = $this->getMockBuilder(PersistentCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->multiCartFacadeInterfaceMock = $this->getMockBuilder(MultiCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMock = $this->getMockBuilder(ItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransfers = new ArrayObject([
            $this->itemTransferMock,
        ]);

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteCreatorByDeliveryDate = new QuoteCreatorByDeliveryDate(
            $this->persistentCartFacadeInterfaceMock,
            $this->multiCartFacadeInterfaceMock
        );
    }

    /**
     * @return void
     */
    public function testCreateAndPersistChildQuotesByDeliveryDate(): void
    {
        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->persistentCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeast(3))
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->atLeast(3))
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('add')
            ->willReturn($this->quoteResponseTransferMock);

        $this->multiCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('setDefaultQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->assertInstanceOf(QuoteCollectionTransfer::class, $this->quoteCreatorByDeliveryDate->createAndPersistChildQuotesByDeliveryDate($this->quoteTransferMock));
        $this->assertInstanceOf(QuoteTransfer::class, $this->quoteCreatorByDeliveryDate->getOriginalQuoteTransfer());
    }

    /**
     * @return void
     */
    public function testCreateAndPersistChildQuotesByDeliveryDateCouldNotResetOriginalQuoteToDefault(): void
    {
        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->persistentCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeast(2))
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('add')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        try {
            $this->quoteCreatorByDeliveryDate->createAndPersistChildQuotesByDeliveryDate($this->quoteTransferMock);
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testCreateAndPersistChildQuotesByDeliveryDateCouldNotAddItems(): void
    {
        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->persistentCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        try {
            $this->quoteCreatorByDeliveryDate->createAndPersistChildQuotesByDeliveryDate($this->quoteTransferMock);
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testCreateAndPersistChildQuotesByDeliveryDateCouldNotCreateQuote(): void
    {
        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->persistentCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(false);

        try {
            $this->quoteCreatorByDeliveryDate->createAndPersistChildQuotesByDeliveryDate($this->quoteTransferMock);
        } catch (Exception $e) {
        }
    }
}
