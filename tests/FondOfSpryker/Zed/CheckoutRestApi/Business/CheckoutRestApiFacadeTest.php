<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessorInterface;
use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;

class CheckoutRestApiFacadeTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CheckoutRestApi\Business\CheckoutRestApiFacade
     */
    protected $checkoutRestApiFacade;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CheckoutRestApi\Business\CheckoutRestApiBusinessFactory
     */
    protected $checkoutRestApiBusinessFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer
     */
    protected $restCheckoutRequestAttributesTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessorInterface
     */
    protected $placeOrderProcessorInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    protected $restCheckoutMultipleResponseTransferMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->checkoutRestApiBusinessFactoryMock = $this->getMockBuilder(CheckoutRestApiBusinessFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutRequestAttributesTransferMock = $this->getMockBuilder(RestCheckoutRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->placeOrderProcessorInterfaceMock = $this->getMockBuilder(PlaceOrderProcessorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutMultipleResponseTransferMock = $this->getMockBuilder(RestCheckoutMultipleResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiFacade = new CheckoutRestApiFacade();
        $this->checkoutRestApiFacade->setFactory($this->checkoutRestApiBusinessFactoryMock);
    }

    /**
     * @return void
     */
    public function testPlaceOrderSplit(): void
    {
        $this->checkoutRestApiBusinessFactoryMock->expects($this->atLeastOnce())
            ->method('createFondOfPlaceOrderProcessor')
            ->willReturn($this->placeOrderProcessorInterfaceMock);

        $this->placeOrderProcessorInterfaceMock->expects($this->atLeastOnce())
            ->method('placeOrderSplit')
            ->willReturn($this->restCheckoutMultipleResponseTransferMock);

        $this->assertInstanceOf(RestCheckoutMultipleResponseTransfer::class, $this->checkoutRestApiFacade->placeOrderSplit($this->restCheckoutRequestAttributesTransferMock));
    }
}
