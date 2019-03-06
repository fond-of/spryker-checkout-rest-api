<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business;

use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutResponseTransfer;
use Spryker\Zed\CheckoutRestApi\Business\CheckoutRestApiFacadeInterface as SprykerCheckoutRestApiFacadeInterface;

interface CheckoutRestApiFacadeInterface extends SprykerCheckoutRestApiFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutResponseTransfer;
}
