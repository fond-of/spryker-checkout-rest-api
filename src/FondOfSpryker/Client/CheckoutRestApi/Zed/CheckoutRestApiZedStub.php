<?php

declare(strict_types=1);

namespace FondOfspryker\Client\CheckoutRestApi\Zed;

use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutResponseTransfer;
use Spryker\Client\CheckoutRestApi\Zed\CheckoutRestApiZedStub as SprykerCheckoutRestApiZedStub;

class CheckoutRestApiZedStub extends SprykerCheckoutRestApiZedStub implements CheckoutRestApiZedStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\RestCheckoutResponseTransfer $restCheckoutResponseTransfer */
        $restCheckoutResponseTransfer = $this->zedRequestClient->call('/checkout-rest-api/gateway/place-order-split', $restCheckoutRequestAttributesTransfer);

        return $restCheckoutResponseTransfer;
    }
}
