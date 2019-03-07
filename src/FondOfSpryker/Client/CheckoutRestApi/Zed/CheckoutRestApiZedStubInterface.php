<?php

declare(strict_types=1);

namespace FondOfSpryker\Client\CheckoutRestApi\Zed;

use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutResponseTransfer;
use Spryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStubInterface as SprykerCheckoutRestApiZedStubInterface;

interface CheckoutRestApiZedStubInterface extends SprykerCheckoutRestApiZedStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutResponseTransfer;
}
