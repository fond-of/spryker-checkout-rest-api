<?php

namespace FondOfSpryker\Zed\CheckoutRestApi;

use Spryker\Zed\Kernel\Container;
use Spryker\Zed\CheckoutRestApi\CheckoutRestApiDependencyProvider as SprykerCheckoutRestApiDependencyProvider;

/**
 * @method \Spryker\Zed\CheckoutRestApi\CheckoutRestApiConfig getConfig()
 */
class CheckoutRestApiDependencyProvider extends SprykerCheckoutRestApiDependencyProvider
{
    public const FACADE_QUOTE_REAL = 'FACADE_QUOTE_REAL';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addQuoteFacadeReal($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuoteFacadeReal(Container $container): Container
    {
        $container[static::FACADE_QUOTE_REAL] = function (Container $container) {
            return $container->getLocator()->quote()->facade();
        };

        return $container;
    }
}
