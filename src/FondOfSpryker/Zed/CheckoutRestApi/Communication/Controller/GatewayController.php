<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Communication\Controller;

use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Zed\CheckoutRestApi\Communication\Controller\GatewayController as SprykerGatewayController;

/**
 * @method \FondOfSpryker\Zed\CheckoutRestApi\Business\CheckoutRestApiFacadeInterface getFacade()
 */
class GatewayController extends SprykerGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    public function placeOrderSplitAction(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestCheckoutMultipleResponseTransfer
    {
        return $this->getFacade()->placeOrderSplit($restCheckoutRequestAttributesTransfer);
    }
}
