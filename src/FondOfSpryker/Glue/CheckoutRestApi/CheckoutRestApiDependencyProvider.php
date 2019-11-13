<?php

declare(strict_types=1);

namespace FondOfSpryker\Glue\CheckoutRestApi;

use Spryker\Glue\Kernel\Container;
use Pyz\Glue\CheckoutRestApi\CheckoutRestApiDependencyProvider as SprykerCheckoutRestApiDependencyProvider;

/**
 * @method \FondOfSpryker\Glue\CheckoutRestApi\CheckoutRestApiConfig getConfig()
 */
class CheckoutRestApiDependencyProvider extends SprykerCheckoutRestApiDependencyProvider
{
    public const CLIENT_REST_COMPANY_USER = 'CLIENT_REST_COMPANY_USER';
    public const CLIENT_REST_CARTS = 'CLIENT_REST_CARTS';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addCompanyRestApiClient($container);
        $container = $this->addCartsRestApiClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCompanyRestApiClient(Container $container): Container
    {
        $container[static::CLIENT_REST_COMPANY_USER] = static function (Container $container) {
            return $container->getLocator()->companyUsersRestApi()->client();
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
        $container[static::CLIENT_REST_CARTS] = static function (Container $container) {
            return $container->getLocator()->cartsRestApi()->client();
        };

        return $container;
    }
}
