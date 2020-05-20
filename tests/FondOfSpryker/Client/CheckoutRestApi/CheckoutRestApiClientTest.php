<?php

namespace FondOfSpryker\Client\CheckoutRestApi;

use Codeception\Test\Unit;
use FondOfSpryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStub;
use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;

class CheckoutRestApiClientTest extends Unit
{
    /**
     * @var \FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClient
     */
    protected $checkoutRestApiClient;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfspryker\Client\CheckoutRestApi\CheckoutRestApiFactory
     */
    protected $checkoutRestApiFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer
     */
    protected $restCheckoutRequestAttributesTransfer;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStub
     */
    protected $checkoutRestApiZedStub;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    protected $restCheckoutMultipleResponseTransfer;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->restCheckoutRequestAttributesTransfer = $this->getMockBuilder(RestCheckoutRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiFactoryMock = $this->getMockBuilder(CheckoutRestApiFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiZedStub = $this->getMockBuilder(CheckoutRestApiZedStub::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutMultipleResponseTransfer = $this->getMockBuilder(RestCheckoutMultipleResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiClient = new CheckoutRestApiClient();
        $this->checkoutRestApiClient->setFactory($this->checkoutRestApiFactoryMock);
    }

    /**
     * @return void
     */
    public function testPlaceOrderSplit(): void
    {
        $this->checkoutRestApiFactoryMock->expects($this->atLeastOnce())
            ->method('createFondOfCheckoutRestApiZedStub')
            ->willReturn($this->checkoutRestApiZedStub);

        $this->checkoutRestApiZedStub->expects($this->atLeastOnce())
            ->method('placeOrderSplit')
            ->willReturn($this->restCheckoutMultipleResponseTransfer);

        $this->assertInstanceOf(RestCheckoutMultipleResponseTransfer::class, $this->checkoutRestApiClient->placeOrderSplit($this->restCheckoutRequestAttributesTransfer));
    }
}
