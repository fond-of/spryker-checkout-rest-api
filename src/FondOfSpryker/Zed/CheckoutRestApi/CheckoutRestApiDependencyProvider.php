<?php

namespace FondOfSpryker\Zed\CheckoutRestApi;

use Spryker\Zed\CheckoutRestApi\CheckoutRestApiDependencyProvider as SprykerCheckoutRestApiDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\CheckoutRestApi\CheckoutRestApiConfig getConfig()
 */
class CheckoutRestApiDependencyProvider extends SprykerCheckoutRestApiDependencyProvider
{
    public const FACADE_PERSISTENT_CART = 'FACADE_PERSISTENT_CART';
    public const FACADE_MULTI_CART = 'FACADE_MULTI_CART';
    public const FACADE_QUOTE_REAL = 'FACADE_QUOTE_REAL';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addPersistentCartFacade($container);
        $container = $this->addMultiCartFacade($container);
        $container = $this->addQuoteFacadeReal($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPersistentCartFacade(Container $container): Container
    {
        $container[static::FACADE_PERSISTENT_CART] = function (Container $container) {
            return $container->getLocator()->persistentCart()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMultiCartFacade(Container $container): Container
    {
        $container[static::FACADE_MULTI_CART] = function (Container $container) {
            return $container->getLocator()->multiCart()->facade();
        };

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
