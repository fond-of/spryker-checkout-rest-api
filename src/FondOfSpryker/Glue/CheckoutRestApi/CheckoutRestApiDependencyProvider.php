<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CheckoutRestApi;

use Spryker\Glue\CheckoutRestApi\CheckoutRestApiDependencyProvider as SprykerCheckoutRestApiDependencyProvider;
use Spryker\Glue\Kernel\Container;

/**
 * @method \FondOfSpryker\Glue\CheckoutRestApi\CheckoutRestApiConfig getConfig()
 */
class CheckoutRestApiDependencyProvider extends SprykerCheckoutRestApiDependencyProvider
{
    public const CLIENT_COMPANY_USER_REFERENCE = 'CLIENT_COMPANY_USER_REFERENCE';
    public const CLIENT_CARTS_REST_API = 'CLIENT_CARTS_REST_API';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addCompanyUserReferenceClient($container);
        $container = $this->addCartsRestApiClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCompanyUserReferenceClient(Container $container): Container
    {
        $container[static::CLIENT_COMPANY_USER_REFERENCE] = static function (Container $container) {
            return $container->getLocator()->companyUserReference()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCartsRestApiClient(Container $container): Container
    {
        $container[static::CLIENT_CARTS_REST_API] = static function (Container $container) {
            return $container->getLocator()->cartsRestApi()->client();
        };

        return $container;
    }
}
