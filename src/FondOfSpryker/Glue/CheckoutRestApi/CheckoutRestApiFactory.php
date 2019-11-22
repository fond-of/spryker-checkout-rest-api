<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CheckoutRestApi;

use FondOfSpryker\Client\CompanyUserReference\CompanyUserReferenceClientInterface;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessor;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessorInterface;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiError;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiErrorInterface;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CheckoutRestApi\CheckoutRestApiFactory as SprykerCheckoutRestApiFactory;

/**
 * @method \FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface getClient()
 * @method \FondOfSpryker\Glue\CheckoutRestApi\CheckoutRestApiConfig getConfig()
 */
class CheckoutRestApiFactory extends SprykerCheckoutRestApiFactory
{
    /**
     * @return \FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessorInterface
     */
    public function createFondOfCheckoutProcessor(): CheckoutProcessorInterface
    {
        return new CheckoutProcessor(
            $this->getClient(),
            $this->getResourceBuilder(),
            $this->createCheckoutRequestAttributesExpander(),
            $this->createCheckoutRequestValidator(),
            $this->createRestCheckoutErrorMapper(),
            $this->createCheckoutResponseMapper(),
            $this->getCompanyUserReferenceClient(),
            $this->getCartsRestApiClient(),
            $this->getRestApiError()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiErrorInterface
     */
    protected function getRestApiError(): RestApiErrorInterface
    {
        return new RestApiError();
    }

    /**
     * @throws
     *
     * @return \Spryker\Client\CartsRestApi\CartsRestApiClientInterface
     */
    protected function getCartsRestApiClient(): CartsRestApiClientInterface
    {
        return $this->getProvidedDependency(CheckoutRestApiDependencyProvider::CLIENT_CARTS_REST_API);
    }

    /**
     * @throws
     *
     * @return \FondOfSpryker\Client\CompanyUserReference\CompanyUserReferenceClientInterface
     */
    protected function getCompanyUserReferenceClient(): CompanyUserReferenceClientInterface
    {
        return $this->getProvidedDependency(CheckoutRestApiDependencyProvider::CLIENT_COMPANY_USER_REFERENCE);
    }
}
