<?php

namespace FondOfSpryker\Client\CheckoutRestApi\Zed;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Client\CheckoutRestApi\Dependency\Client\CheckoutRestApiToZedRequestClientInterface;

class CheckoutRestApiZedStubTest extends Unit
{
    /**
     * @var \FondOfSpryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStub
     */
    protected $checkoutRestApiZedStub;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer
     */
    protected $restCheckoutRequestAttributesTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Client\CheckoutRestApi\Dependency\Client\CheckoutRestApiToZedRequestClientInterface
     */
    protected $checkoutRestApiToZedRequestClientInterfaceMock;

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

        $this->checkoutRestApiToZedRequestClientInterfaceMock = $this->getMockBuilder(CheckoutRestApiToZedRequestClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutRequestAttributesTransferMock = $this->getMockBuilder(RestCheckoutRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutMultipleResponseTransferMock = $this->getMockBuilder(RestCheckoutMultipleResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiZedStub = new CheckoutRestApiZedStub($this->checkoutRestApiToZedRequestClientInterfaceMock);
    }

    /**
     * @return void
     */
    public function testPlaceOrderSplit(): void
    {
        $this->checkoutRestApiToZedRequestClientInterfaceMock->expects($this->atLeastOnce())
            ->method('call')
            ->willReturn($this->restCheckoutMultipleResponseTransferMock);

        $this->assertInstanceOf(RestCheckoutMultipleResponseTransfer::class, $this->checkoutRestApiZedStub->placeOrderSplit($this->restCheckoutRequestAttributesTransferMock));
    }
}
