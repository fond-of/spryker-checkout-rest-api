<?php

namespace FondOfSpryker\Client\CheckoutRestApi;

use Codeception\Test\Unit;
use Spryker\Client\Kernel\Container;

class CheckoutRestApiDependencyProviderTest extends Unit
{
    /**
     * @var \FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiDependencyProvider
     */
    protected $checkoutRestApiDependencyProvider;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Client\Kernel\Container
     */
    protected $containerMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiDependencyProvider = new CheckoutRestApiDependencyProvider();
    }

    /**
     * @return void
     */
    public function testProvideServiceLayerDependencies(): void
    {
        $this->assertInstanceOf(Container::class, $this->checkoutRestApiDependencyProvider->provideServiceLayerDependencies($this->containerMock));
    }
}
