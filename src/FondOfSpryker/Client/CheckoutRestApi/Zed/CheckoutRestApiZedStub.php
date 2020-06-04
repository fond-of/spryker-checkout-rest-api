<?php

declare(strict_types = 1);

namespace FondOfSpryker\Client\CheckoutRestApi\Zed;

use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStub as SprykerCheckoutRestApiZedStub;

class CheckoutRestApiZedStub extends SprykerCheckoutRestApiZedStub implements CheckoutRestApiZedStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    public function placeOrderSplit(
        RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
    ): RestCheckoutMultipleResponseTransfer {
        /** @var \Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer $restCheckoutResponseTransfer */
        $restCheckoutResponseTransfer = $this->zedRequestClient->call('/checkout-rest-api/gateway/place-order-split', $restCheckoutRequestAttributesTransfer);

        return $restCheckoutResponseTransfer;
    }
}
