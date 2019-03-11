<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business;

use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Zed\CheckoutRestApi\Business\CheckoutRestApiFacade as SprykerCheckoutRestApiFacade;

/**
 * @method \FondOfSpryker\Zed\CheckoutRestApi\Business\CheckoutRestApiBusinessFactory getFactory()
 */
class CheckoutRestApiFacade extends SprykerCheckoutRestApiFacade implements CheckoutRestApiFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    public function placeOrderSplit(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutMultipleResponseTransfer
    {
        return $this->getFactory()->createFondOfPlaceOrderProcessor()->placeOrderSplit($restCheckoutRequestAttributesTransfer);
    }
}
