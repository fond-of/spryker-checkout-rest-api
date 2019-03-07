<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Communication\Controller;

use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutResponseTransfer;

use Spryker\Zed\CheckoutRestApi\Communication\Controller\GatewayController as SprykerGatewayController;

/**
 * @method \FondOfSpryker\Zed\CheckoutRestApi\Business\CheckoutRestApiFacadeInterface getFacade()
 */
class GatewayController extends SprykerGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    public function placeOrderSplitAction(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutResponseTransfer
    {
        return $this->getFacade()->placeOrderSplit($restCheckoutRequestAttributesTransfer);
    }
}
