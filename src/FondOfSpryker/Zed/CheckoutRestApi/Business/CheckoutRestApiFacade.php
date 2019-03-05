<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business;

use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutResponseTransfer;
use Spryker\Zed\CheckoutRestApi\Business\CheckoutRestApiFacade as SprykerCheckoutRestApiFacade;

/**
 * @method \FondOfSpryker\Zed\CheckoutRestApi\Business\CheckoutRestApiBusinessFactory getFactory()
 */
class CheckoutRestApiFacade extends SprykerCheckoutRestApiFacade implements CheckoutRestApiFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    public function placeFondOfOrder(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutResponseTransfer
    {
        return $this->getFactory()->createFondOfPlaceOrderProcessor()->placeOrder($restCheckoutRequestAttributesTransfer);
    }
}
