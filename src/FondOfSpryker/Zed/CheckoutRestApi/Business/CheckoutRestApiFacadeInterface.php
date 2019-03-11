<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business;

use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Zed\CheckoutRestApi\Business\CheckoutRestApiFacadeInterface as SprykerCheckoutRestApiFacadeInterface;

interface CheckoutRestApiFacadeInterface extends SprykerCheckoutRestApiFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutMultipleResponseTransfer;
}
