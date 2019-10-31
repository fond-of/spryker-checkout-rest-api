<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use ReflectionClass;
use ReflectionMethod;
use Spryker\Zed\CheckoutRestApi\Business\Checkout\Quote\QuoteReaderInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCalculationFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCartFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCheckoutFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToQuoteFacadeInterface;
use Spryker\Zed\Quote\Business\QuoteFacadeInterface;

class PlaceOrderProcessorTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessor
     */
    protected $placeOrderProcessor;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Business\Checkout\Quote\QuoteReaderInterface
     */
    protected $quoteReaderInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCartFacadeInterface
     */
    protected $checkoutRestApiToCartFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCheckoutFacadeInterface
     */
    protected $checkoutRestApiToCheckoutFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToQuoteFacadeInterface
     */
    protected $checkoutRestApiToQuoteFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCalculationFacadeInterface
     */
    protected $checkoutRestApiToCalculationFacadeInterfaceMock;

    /**
     * @var array
     */
    protected $quoteMapperPlugins;

    /**
     * @var array
     */
    protected $checkoutDataValidatorPlugins;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDateInterface
     */
    protected $quoteCreatorByDeliveryDateInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Quote\Business\QuoteFacadeInterface
     */
    protected $quoteFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer
     */
    protected $restCheckoutRequestAttributesTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var array
     */
    protected $itemTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\ItemTransfer
     */
    protected $itemTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteResponseTransfer
     */
    protected $quoteResponseTransferMock;

    /**
     * @var array
     */
    protected $quoteErrorTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteErrorTransfer
     */
    protected $quoteErrorTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    protected $quoteCollectionTransferMock;

    /**
     * @var \ArrayObject
     */
    protected $quoteTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected $checkoutResponseTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\SaveOrderTransfer
     */
    protected $saveOrderTransferMock;

    /**
     * @var int
     */
    protected $idSalesOrder;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->quoteReaderInterfaceMock = $this->getMockBuilder(QuoteReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToCartFacadeInterfaceMock = $this->getMockBuilder(CheckoutRestApiToCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToCheckoutFacadeInterfaceMock = $this->getMockBuilder(CheckoutRestApiToCheckoutFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToQuoteFacadeInterfaceMock = $this->getMockBuilder(CheckoutRestApiToQuoteFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToCalculationFacadeInterfaceMock = $this->getMockBuilder(CheckoutRestApiToCalculationFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteMapperPlugins = [

        ];

        $this->checkoutDataValidatorPlugins = [

        ];

        $this->quoteCreatorByDeliveryDateInterfaceMock = $this->getMockBuilder(QuoteCreatorByDeliveryDateInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteFacadeInterfaceMock = $this->getMockBuilder(QuoteFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutRequestAttributesTransferMock = $this->getMockBuilder(RestCheckoutRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMock = $this->getMockBuilder(ItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransfers = [
            $this->itemTransferMock,
        ];

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteErrorTransferMock = $this->getMockBuilder(QuoteErrorTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteErrorTransfers = new ArrayObject([
            $this->quoteErrorTransferMock,
        ]);

        $this->quoteCollectionTransferMock = $this->getMockBuilder(QuoteCollectionTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransfers = new ArrayObject([
            $this->quoteTransferMock,
        ]);

        $this->checkoutResponseTransferMock = $this->getMockBuilder(CheckoutResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->saveOrderTransferMock = $this->getMockBuilder(SaveOrderTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->idSalesOrder = 1;

        $this->placeOrderProcessor = new PlaceOrderProcessor(
            $this->quoteReaderInterfaceMock,
            $this->checkoutRestApiToCartFacadeInterfaceMock,
            $this->checkoutRestApiToCheckoutFacadeInterfaceMock,
            $this->checkoutRestApiToQuoteFacadeInterfaceMock,
            $this->checkoutRestApiToCalculationFacadeInterfaceMock,
            $this->quoteMapperPlugins,
            $this->checkoutDataValidatorPlugins,
            $this->quoteCreatorByDeliveryDateInterfaceMock,
            $this->quoteFacadeInterfaceMock
        );
    }

    /**
     * @return void
     */
    public function testHandlePlaceOrderSplit(): void
    {
        $reflectionMethod = $this->getReflectionMethodByName('handlePlaceOrderSplit');

        $this->quoteReaderInterfaceMock->expects($this->atLeastOnce())
            ->method('findCustomerQuoteByUuid')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->checkoutRestApiToCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('validateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->checkoutRestApiToCalculationFacadeInterfaceMock->expects($this->atLeast(2))
            ->method('recalculateQuote')
            ->willReturn($this->quoteTransferMock);

        $this->quoteCreatorByDeliveryDateInterfaceMock->expects($this->atLeastOnce())
            ->method('createAndPersistChildQuotesByDeliveryDate')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->checkoutRestApiToCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('validateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->checkoutRestApiToCheckoutFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('placeOrder')
            ->willReturn($this->checkoutResponseTransferMock);

        $this->checkoutResponseTransferMock->expects($this->atLeastOnce())
            ->method('getSaveOrder')
            ->willReturn($this->saveOrderTransferMock);

        $this->saveOrderTransferMock->expects($this->atLeastOnce())
            ->method('getIdSalesOrder')
            ->willReturn($this->idSalesOrder);

        $this->checkoutRestApiToQuoteFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('deleteQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->assertInstanceOf(
            RestCheckoutMultipleResponseTransfer::class,
            $reflectionMethod->invokeArgs($this->placeOrderProcessor, [$this->restCheckoutRequestAttributesTransferMock])
        );
    }

    /**
     * @return void
     */
    public function testHandlePlaceOrderSplitQuoteTransferInvalidErrorsNull(): void
    {
        $reflectionMethod = $this->getReflectionMethodByName('handlePlaceOrderSplit');

        $this->quoteReaderInterfaceMock->expects($this->atLeastOnce())
            ->method('findCustomerQuoteByUuid')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->checkoutRestApiToCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('validateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(false);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getErrors')
            ->willReturn(new ArrayObject([]));

        $this->assertInstanceOf(
            RestCheckoutMultipleResponseTransfer::class,
            $reflectionMethod->invokeArgs($this->placeOrderProcessor, [$this->restCheckoutRequestAttributesTransferMock])
        );
    }

    /**
     * @return void
     */
    public function testHandlePlaceOrderSplitOnlyFirstQuoteResponseTransferIsSuccessful(): void
    {
        $reflectionMethod = $this->getReflectionMethodByName('handlePlaceOrderSplit');

        $this->quoteReaderInterfaceMock->expects($this->atLeastOnce())
            ->method('findCustomerQuoteByUuid')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->checkoutRestApiToCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('validateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->checkoutRestApiToCalculationFacadeInterfaceMock->expects($this->atLeast(2))
            ->method('recalculateQuote')
            ->willReturn($this->quoteTransferMock);

        $this->quoteCreatorByDeliveryDateInterfaceMock->expects($this->atLeastOnce())
            ->method('createAndPersistChildQuotesByDeliveryDate')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->checkoutRestApiToCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('validateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->checkoutRestApiToCheckoutFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('placeOrder')
            ->willReturn($this->checkoutResponseTransferMock);

        $this->checkoutResponseTransferMock->expects($this->atLeastOnce())
            ->method('getSaveOrder')
            ->willReturn($this->saveOrderTransferMock);

        $this->saveOrderTransferMock->expects($this->atLeastOnce())
            ->method('getIdSalesOrder')
            ->willReturnOnConsecutiveCalls($this->idSalesOrder, null);

        $this->quoteFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('updateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->checkoutRestApiToQuoteFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('deleteQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->assertInstanceOf(
            RestCheckoutMultipleResponseTransfer::class,
            $reflectionMethod->invokeArgs($this->placeOrderProcessor, [$this->restCheckoutRequestAttributesTransferMock])
        );
    }

    /**
     * @return void
     */
    public function testHandlePlaceOrderSplitOnlyFirstQuoteResponseTransferIsSuccessfulUpdateException(): void
    {
        $reflectionMethod = $this->getReflectionMethodByName('handlePlaceOrderSplit');

        $this->quoteReaderInterfaceMock->expects($this->atLeastOnce())
            ->method('findCustomerQuoteByUuid')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->checkoutRestApiToCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('validateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->checkoutRestApiToCalculationFacadeInterfaceMock->expects($this->atLeast(2))
            ->method('recalculateQuote')
            ->willReturn($this->quoteTransferMock);

        $this->quoteCreatorByDeliveryDateInterfaceMock->expects($this->atLeastOnce())
            ->method('createAndPersistChildQuotesByDeliveryDate')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->checkoutRestApiToCartFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('validateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturnOnConsecutiveCalls(true, true, false);

        $this->quoteCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->checkoutRestApiToCheckoutFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('placeOrder')
            ->willReturn($this->checkoutResponseTransferMock);

        $this->checkoutResponseTransferMock->expects($this->atLeastOnce())
            ->method('getSaveOrder')
            ->willReturn($this->saveOrderTransferMock);

        $this->saveOrderTransferMock->expects($this->atLeastOnce())
            ->method('getIdSalesOrder')
            ->willReturnOnConsecutiveCalls($this->idSalesOrder, null);

        $this->quoteFacadeInterfaceMock->expects($this->atLeastOnce())
            ->method('updateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->assertInstanceOf(
            RestCheckoutMultipleResponseTransfer::class,
            $reflectionMethod->invokeArgs($this->placeOrderProcessor, [$this->restCheckoutRequestAttributesTransferMock])
        );
    }

    /**
     * @param string $name
     *
     * @throws
     *
     * @return \ReflectionMethod
     */
    protected function getReflectionMethodByName(string $name): ReflectionMethod
    {
        $reflectionClass = new ReflectionClass(PlaceOrderProcessor::class);

        $reflectionMethod = $reflectionClass->getMethod($name);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod;
    }
}
