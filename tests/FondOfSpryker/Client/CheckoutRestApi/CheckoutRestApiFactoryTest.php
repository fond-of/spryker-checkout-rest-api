<?php

namespace FondOfSpryker\Client\CheckoutRestApi;

use Codeception\Test\Unit;
use FondOfSpryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStubInterface;
use Spryker\Client\CheckoutRestApi\CheckoutRestApiDependencyProvider;
use Spryker\Client\CheckoutRestApi\Dependency\Client\CheckoutRestApiToZedRequestClientInterface;
use Spryker\Client\Kernel\Container;

class CheckoutRestApiFactoryTest extends Unit
{
    /**
     * @var \FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiFactory
     */
    protected $checkoutRestApiFactory;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Client\Kernel\Container
     */
    protected $containerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Client\CheckoutRestApi\Dependency\Client\CheckoutRestApiToZedRequestClientInterface
     */
    protected $checkoutRestApiToZedRequestClientInterfaceMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToZedRequestClientInterfaceMock = $this->getMockBuilder(CheckoutRestApiToZedRequestClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiFactory = new CheckoutRestApiFactory();
        $this->checkoutRestApiFactory->setContainer($this->containerMock);
    }

    /**
     * @return void
     */
    public function testCreateFondOfCheckoutRestApiZedStub(): void
    {
        $this->containerMock->expects($this->atLeastOnce())
            ->method('has')
            ->willReturn(true);

        $this->containerMock->expects($this->atLeastOnce())
            ->method('get')
            ->with(CheckoutRestApiDependencyProvider::CLIENT_ZED_REQUEST)
            ->willReturn($this->checkoutRestApiToZedRequestClientInterfaceMock);

        $this->assertInstanceOf(CheckoutRestApiZedStubInterface::class, $this->checkoutRestApiFactory->createFondOfCheckoutRestApiZedStub());
    }
}
