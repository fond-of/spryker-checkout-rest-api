<?php

declare(strict_types=1);

namespace FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout;

use FondOfSpryker\Client\CheckoutPermission\Plugin\Permission\PlaceOrderPermissionPlugin;
use FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface;
use FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiErrorInterface;
use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCheckoutMultipleResponseAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestUserTransfer;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CheckoutRestApi\CheckoutRestApiConfig;
use Spryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutResponseMapperInterface;
use Spryker\Glue\CheckoutRestApi\Processor\Error\RestCheckoutErrorMapperInterface;
use Spryker\Glue\CheckoutRestApi\Processor\RequestAttributesExpander\CheckoutRequestAttributesExpanderInterface;
use Spryker\Glue\CheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessor as SprykerCheckoutProcessor;
use Spryker\Glue\Kernel\PermissionAwareTrait;

class CheckoutProcessor extends SprykerCheckoutProcessor implements CheckoutProcessorInterface
{
    use PermissionAwareTrait;

    /**
     * @var \FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface
     */
    protected $fondOfCheckoutRestApiClient;

    /**
     * @var \FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface
     */
    protected $companyUsersRestApiClient;

    /**
     * @var \Spryker\Client\CartsRestApi\CartsRestApiClientInterface
     */
    protected $cartsRestApiClient;

    /**
     * @var \FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiErrorInterface
     */
    private $restApiError;

    /**
     * @param \FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface $fondOfCheckoutRestApiClient
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \Spryker\Glue\CheckoutRestApi\Processor\RequestAttributesExpander\CheckoutRequestAttributesExpanderInterface $checkoutRequestAttributesExpander
     * @param \Spryker\Glue\CheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface $checkoutRequestValidator
     * @param \Spryker\Glue\CheckoutRestApi\Processor\Error\RestCheckoutErrorMapperInterface $restCheckoutErrorMapper
     * @param \Spryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutResponseMapperInterface $checkoutResponseMapper
     * @param \FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface $companyUsersRestApiClient
     * @param \Spryker\Client\CartsRestApi\CartsRestApiClientInterface $cartsRestApiClient
     * @param \FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiErrorInterface $restApiError
     */
    public function __construct(
        CheckoutRestApiClientInterface $fondOfCheckoutRestApiClient,
        RestResourceBuilderInterface $restResourceBuilder,
        CheckoutRequestAttributesExpanderInterface $checkoutRequestAttributesExpander,
        CheckoutRequestValidatorInterface $checkoutRequestValidator,
        RestCheckoutErrorMapperInterface $restCheckoutErrorMapper,
        CheckoutResponseMapperInterface $checkoutResponseMapper,
        CompanyUsersRestApiClientInterface $companyUsersRestApiClient,
        CartsRestApiClientInterface $cartsRestApiClient,
        RestApiErrorInterface $restApiError
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
        $this->companyUsersRestApiClient = $companyUsersRestApiClient;
        $this->cartsRestApiClient = $cartsRestApiClient;
        $this->restApiError = $restApiError;
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

        $hasPermission = $this->hasPermissionToPlaceOrder(
            $restCheckoutRequestAttributesTransfer->getIdCart(),
            $restRequest->getRestUser()
        );

        if (!$hasPermission) {
            return $this->restApiError->addPermissionDeniedErrorResponse(
                $this->restResourceBuilder->createRestResponse()
            );
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
     * @param string $uuid
     * @param \Generated\Shared\Transfer\RestUserTransfer $restUserTransfer
     *
     * @return bool
     */
    protected function hasPermissionToPlaceOrder(string $uuid, RestUserTransfer $restUserTransfer): bool
    {

        $quoteResponseTransfer = $this->findQuoteByUuid($uuid, $restUserTransfer);
        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return false;
        }

        $companyUserResponseTransfer = $this->findCompanyUserByUuid(
            $quoteResponseTransfer->getQuoteTransfer()->getCompanyUserReference()
        );

        if (!$companyUserResponseTransfer->getIsSuccessful()) {
            return false;
        }

        return $this->can(PlaceOrderPermissionPlugin::KEY, $companyUserResponseTransfer->getCompanyUser()->getFkCompany());
    }

    /**
     * @param string $uuid
     * @param \Generated\Shared\Transfer\RestUserTransfer $restUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    protected function findQuoteByUuid(string $uuid, RestUserTransfer $restUserTransfer): QuoteResponseTransfer
    {
        return $this->cartsRestApiClient->findQuoteByUuid(
            (new QuoteTransfer())
                ->setUuid($uuid)
                ->setCustomerReference($restUserTransfer->getNaturalIdentifier())
                ->setCustomer((new CustomerTransfer())->setCustomerReference($restUserTransfer->getNaturalIdentifier()))
        );
    }

    /**
     * @param string $companyUserReference
     *
     * @return \Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    protected function findCompanyUserByUuid(string $companyUserReference): CompanyUserResponseTransfer
    {
        return $this->companyUsersRestApiClient->findCompanyUserByCompanyUserReference(
            (new CompanyUserTransfer())->setCompanyUserReference($companyUserReference)
        );
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
