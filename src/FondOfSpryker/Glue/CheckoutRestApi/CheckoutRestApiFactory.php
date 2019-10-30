<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CheckoutRestApi;

use FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessor;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessorInterface;
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
            $this->createCheckoutResponseMapper()
        );
    }
}
