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
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';
    public const CLIENT_CART = 'CLIENT_CART';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addQuoteFacadeReal($container);
        $container = $this->addQuoteClient($container);
        $container = $this->addCartClient($container);

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

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuoteClient(Container $container): Container
    {
        $container[static::CLIENT_QUOTE] = function (Container $container) {
            return $container->getLocator()->quote()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCartClient(Container $container): Container
    {
        $container[static::CLIENT_CART] = function (Container $container) {
            return $container->getLocator()->cart()->client();
        };

        return $container;
    }
}
