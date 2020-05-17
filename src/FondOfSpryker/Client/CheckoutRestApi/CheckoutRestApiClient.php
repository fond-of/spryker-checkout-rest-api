<?php

declare(strict_types = 1);

namespace FondOfSpryker\Client\CheckoutRestApi;

use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Client\CheckoutRestApi\CheckoutRestApiClient as SprykerCheckoutRestApiClient;

/**
 * @method \FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiFactory getFactory()
 */
class CheckoutRestApiClient extends SprykerCheckoutRestApiClient implements CheckoutRestApiClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    public function placeOrderSplit(
        RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
    ): RestCheckoutMultipleResponseTransfer {
        return $this->getFactory()
            ->createFondOfCheckoutRestApiZedStub()->placeOrderSplit($restCheckoutRequestAttributesTransfer);
    }
}
