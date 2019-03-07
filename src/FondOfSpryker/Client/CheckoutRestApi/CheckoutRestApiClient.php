<?php

declare(strict_types=1);

namespace FondOfSpryker\Client\CheckoutRestApi;

use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutResponseTransfer;
use Spryker\Client\CheckoutRestApi\CheckoutRestApiClient as SprykerCheckoutRestApiClient;

/**
 * @method \FondOfspryker\Client\CheckoutRestApi\CheckoutRestApiFactory getFactory()
 */
class CheckoutRestApiClient extends SprykerCheckoutRestApiClient implements CheckoutRestApiClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutResponseTransfer
    {
        return $this->getFactory()->createFondOfCheckoutRestApiZedStub()->placeOrderSplit($restCheckoutRequestAttributesTransfer);
    }
}
