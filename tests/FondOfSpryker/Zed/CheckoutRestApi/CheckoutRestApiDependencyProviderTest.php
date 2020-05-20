<?php

namespace FondOfSpryker\Zed\CheckoutRestApi;

use Codeception\Test\Unit;
use Spryker\Zed\Kernel\Container;

class CheckoutRestApiDependencyProviderTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CheckoutRestApi\CheckoutRestApiDependencyProvider
     */
    protected $checkoutRestApiDependencyProviderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CheckoutRestApi\CheckoutRestApiConfig
     */
    protected $checkoutRestApiConfigMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Kernel\Container
     */
    protected $containerMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->checkoutRestApiConfigMock = $this->getMockBuilder(CheckoutRestApiConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiDependencyProviderMock = new CheckoutRestApiDependencyProvider();
        $this->checkoutRestApiDependencyProviderMock->setConfig($this->checkoutRestApiConfigMock);
    }

    /**
     * @return void
     */
    public function testProvideBusinessLayerDependencies(): void
    {
        $this->assertInstanceOf(Container::class, $this->checkoutRestApiDependencyProviderMock->provideBusinessLayerDependencies($this->containerMock));
    }
}
