<?php

declare(strict_types = 1);

namespace FondOfSpryker\Client\CheckoutRestApi\Zed;

use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStubInterface as SprykerCheckoutRestApiZedStubInterface;

interface CheckoutRestApiZedStubInterface extends SprykerCheckoutRestApiZedStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutMultipleResponseTransfer;
}
