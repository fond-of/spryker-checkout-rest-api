<?php

declare(strict_types = 1);

namespace FondOfSpryker\Client\CheckoutRestApi;

use FondOfSpryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStub;
use Spryker\Client\CheckoutRestApi\CheckoutRestApiFactory as SprykerCheckoutRestApiFactory;
use Spryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStubInterface;

class CheckoutRestApiFactory extends SprykerCheckoutRestApiFactory
{
    /**
     * @return \FondOfSpryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStubInterface
     */
    public function createFondOfCheckoutRestApiZedStub(): CheckoutRestApiZedStubInterface
    {
        return new CheckoutRestApiZedStub($this->getZedRequestClient());
    }
}
