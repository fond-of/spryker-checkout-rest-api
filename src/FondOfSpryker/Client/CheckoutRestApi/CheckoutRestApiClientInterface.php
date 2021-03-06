<?php

declare(strict_types=1);

namespace FondOfSpryker\Client\CheckoutRestApi;

use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface as SprykerCheckoutRestApiClientInterface;

interface CheckoutRestApiClientInterface extends SprykerCheckoutRestApiClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutMultipleResponseTransfer;
}
