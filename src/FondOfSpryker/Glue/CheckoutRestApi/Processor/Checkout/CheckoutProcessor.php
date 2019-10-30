<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout;

use FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface;
use Generated\Shared\Transfer\RestCheckoutMultipleResponseAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Glue\CheckoutRestApi\CheckoutRestApiConfig;
use Spryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessor as SprykerCheckoutProcessor;
use Spryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutResponseMapperInterface;
use Spryker\Glue\CheckoutRestApi\Processor\Error\RestCheckoutErrorMapperInterface;
use Spryker\Glue\CheckoutRestApi\Processor\RequestAttributesExpander\CheckoutRequestAttributesExpanderInterface;
use Spryker\Glue\CheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CheckoutProcessor extends SprykerCheckoutProcessor implements CheckoutProcessorInterface
{
    /**
     * @var \FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface
     */
    protected $fondOfCheckoutRestApiClient;

    /**
     * @param \FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface $fondOfCheckoutRestApiClient
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \Spryker\Glue\CheckoutRestApi\Processor\RequestAttributesExpander\CheckoutRequestAttributesExpanderInterface $checkoutRequestAttributesExpander
     * @param \Spryker\Glue\CheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface $checkoutRequestValidator
     * @param \Spryker\Glue\CheckoutRestApi\Processor\Error\RestCheckoutErrorMapperInterface $restCheckoutErrorMapper
     * @param \Spryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutResponseMapperInterface $checkoutResponseMapper
     */
    public function __construct(
        CheckoutRestApiClientInterface $fondOfCheckoutRestApiClient,
        RestResourceBuilderInterface $restResourceBuilder,
        CheckoutRequestAttributesExpanderInterface $checkoutRequestAttributesExpander,
        CheckoutRequestValidatorInterface $checkoutRequestValidator,
        RestCheckoutErrorMapperInterface $restCheckoutErrorMapper,
        CheckoutResponseMapperInterface $checkoutResponseMapper
    ) {
        parent::__construct(
            $fondOfCheckoutRestApiClient,
            $restResourceBuilder,
            $checkoutRequestAttributesExpander,
            $checkoutRequestValidator,
            $restCheckoutErrorMapper,
            $checkoutResponseMapper
        );

        $this->fondOfCheckoutRestApiClient = $fondOfCheckoutRestApiClient;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function placeOrderSplit(RestRequestInterface $restRequest, RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): RestResponseInterface
    {
        $restErrorCollectionTransfer = $this->checkoutRequestValidator->validateCheckoutRequest($restRequest, $restCheckoutRequestAttributesTransfer);
        if ($restErrorCollectionTransfer->getRestErrors()->count()) {
            return $this->createValidationErrorResponse($restErrorCollectionTransfer);
        }

        $restCheckoutRequestAttributesTransfer = $this->checkoutRequestAttributesExpander
            ->expandCheckoutRequestAttributes($restRequest, $restCheckoutRequestAttributesTransfer);

        $restCheckoutMultipleResponseTransfer = $this->fondOfCheckoutRestApiClient->placeOrderSplit($restCheckoutRequestAttributesTransfer);
        if (!$restCheckoutMultipleResponseTransfer->getIsSuccess()) {
            return $this->createPlaceOrderFailedErrorResponse($restCheckoutMultipleResponseTransfer->getErrors(), $restRequest->getMetadata()->getLocale());
        }

        return $this->createOrderPlacedMultipleResponse($restCheckoutMultipleResponseTransfer->getOrderReferences());
    }

    /**
     * @param string[] $orderReferences
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function createOrderPlacedMultipleResponse(array $orderReferences): RestResponseInterface
    {
        $restCheckoutMultipleResponseAttributesTransfer = new RestCheckoutMultipleResponseAttributesTransfer();
        $restCheckoutMultipleResponseAttributesTransfer->setOrderReferences($orderReferences);

        $restResource = $this->restResourceBuilder->createRestResource(
            CheckoutRestApiConfig::RESOURCE_CHECKOUT,
            null,
            $restCheckoutMultipleResponseAttributesTransfer
        );

        return $this->restResourceBuilder
            ->createRestResponse()
            ->addResource($restResource);
    }
}
