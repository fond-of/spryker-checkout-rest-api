<?php

declare(strict_types=1);

namespace FondOfSpryker\Glue\CheckoutRestApi\Controller;

use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\Kernel\Controller\AbstractController;

/**
 * @method \FondOfSpryker\Glue\CheckoutRestApi\CheckoutRestApiFactory getFactory()
 */
class CheckoutResourceController extends AbstractController
{
    /**
     * @Glue({
     *     "post": {
     *          "summary": [
     *              "Places orders and split by delivery date."
     *          ],
     *          "parameters": [
     *              {
     *                  "name": "Accept-Language",
     *                  "in": "header"
     *              },
     *              {
     *                  "name": "X-Anonymous-Customer-Unique-Id",
     *                  "in": "header",
     *                  "required": false,
     *                  "description": "Guest customer unique ID"
     *              }
     *          ],
     *          "responses": {
     *              "400": "Bad Response.",
     *              "422": "Unprocessable entity."
     *          },
     *          "responseAttributesClassName": "\\Generated\\Shared\\Transfer\\RestCheckoutResponseAttributesTransfer",
     *          "isIdNullable": true
     *     }
     * })
     *
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function postAction(
        RestRequestInterface $restRequest,
        RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
    ): RestResponseInterface {
        return $this->getFactory()
            ->createFondOfCheckoutProcessor()
            ->placeOrderSplit($restRequest, $restCheckoutRequestAttributesTransfer);
    }
}
