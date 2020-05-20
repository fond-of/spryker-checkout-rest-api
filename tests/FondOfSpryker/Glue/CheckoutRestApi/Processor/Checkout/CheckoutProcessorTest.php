<?php

namespace FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface;
use FondOfSpryker\Client\CompanyUserReference\CompanyUserReferenceClientInterface;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiErrorInterface;
use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCheckoutErrorTransfer;
use Generated\Shared\Transfer\RestCheckoutMultipleResponseAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestErrorCollectionTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Generated\Shared\Transfer\RestUserTransfer;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\CheckoutRestApi\CheckoutRestApiConfig;
use Spryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutResponseMapperInterface;
use Spryker\Glue\CheckoutRestApi\Processor\Error\RestCheckoutErrorMapperInterface;
use Spryker\Glue\CheckoutRestApi\Processor\RequestAttributesExpander\CheckoutRequestAttributesExpanderInterface;
use Spryker\Glue\CheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\MetadataInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class CheckoutProcessorTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessor
     */
    protected $checkoutProcessor;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface
     */
    protected $checkoutRestApiClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\CheckoutRestApi\Processor\RequestAttributesExpander\CheckoutRequestAttributesExpanderInterface
     */
    protected $checkoutRequestAttributesExpanderInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\CheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface
     */
    protected $checkoutRequestValidatorInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\CheckoutRestApi\Processor\Error\RestCheckoutErrorMapperInterface
     */
    protected $restCheckoutErrorMapperInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutResponseMapperInterface
     */
    protected $checkoutResponseMapperInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Client\CompanyUserReference\CompanyUserReferenceClientInterface
     */
    protected $companyUserReferenceClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Client\CartsRestApi\CartsRestApiClientInterface
     */
    protected $cartsRestApiClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiErrorInterface
     */
    protected $restApiErrorMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface
     */
    protected $restRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestUserTransfer
     */
    protected $restUserTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer
     */
    protected $restCheckoutRequestAttributesTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteResponseTransfer
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    protected $companyUserResponseTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CompanyUserTransfer
     */
    protected $companyUserTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\CheckoutRestApi\Processor\Error\RestCheckoutErrorMapperInterface
     */
    protected $restErrorCollectionTransferMock;

    /**
     * @var array
     */
    protected $restErrorMessageTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestErrorMessageTransfer
     */
    protected $restErrorMessageTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected $restResponseMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer
     */
    protected $restCheckoutMultipleResponseTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutErrorTransfer
     */
    protected $restCheckoutErrorTransferMock;

    /**
     * @var \ArrayObject
     */
    protected $restCheckoutErrorTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\MetadataInterface
     */
    protected $metadataInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    private $restResourceMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->checkoutRestApiClientMock = $this->getMockBuilder(CheckoutRestApiClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceBuilderMock = $this->getMockBuilder(RestResourceBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRequestAttributesExpanderInterfaceMock = $this->getMockBuilder(CheckoutRequestAttributesExpanderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRequestValidatorInterfaceMock = $this->getMockBuilder(CheckoutRequestValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutErrorMapperInterfaceMock = $this->getMockBuilder(RestCheckoutErrorMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutResponseMapperInterfaceMock = $this->getMockBuilder(CheckoutResponseMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserReferenceClientMock = $this->getMockBuilder(CompanyUserReferenceClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartsRestApiClientMock = $this->getMockBuilder(CartsRestApiClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restApiErrorMock = $this->getMockBuilder(RestApiErrorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restRequestMock = $this->getMockBuilder(RestRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restUserTransferMock = $this->getMockBuilder(RestUserTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutRequestAttributesTransferMock = $this->getMockBuilder(RestCheckoutRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserResponseTransferMock = $this->getMockBuilder(CompanyUserResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserTransferMock = $this->getMockBuilder(CompanyUserTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restErrorCollectionTransferMock = $this->getMockBuilder(RestErrorCollectionTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restErrorMessageTransferMock = $this->getMockBuilder(RestErrorMessageTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResponseMock = $this->getMockBuilder(RestResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restErrorMessageTransfers = new ArrayObject([
            $this->restErrorMessageTransferMock,
        ]);

        $this->restCheckoutMultipleResponseTransferMock = $this->getMockBuilder(RestCheckoutMultipleResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutErrorTransferMock = $this->getMockBuilder(RestCheckoutErrorTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutErrorTransfers = new ArrayObject([
            $this->restCheckoutErrorTransferMock,
        ]);

        $this->metadataInterfaceMock = $this->getMockBuilder(MetadataInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceMock = $this->getMockBuilder(RestResourceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutProcessor = new class (
            $this->checkoutRestApiClientMock,
            $this->restResourceBuilderMock,
            $this->checkoutRequestAttributesExpanderInterfaceMock,
            $this->checkoutRequestValidatorInterfaceMock,
            $this->restCheckoutErrorMapperInterfaceMock,
            $this->checkoutResponseMapperInterfaceMock,
            $this->companyUserReferenceClientMock,
            $this->cartsRestApiClientMock,
            $this->restApiErrorMock
        ) extends CheckoutProcessor {
            /**
             * @uses \Spryker\Client\Permission\PermissionClientInterface
             *
             * @param string $permissionKey
             * @param string|int|array|null $context
             *
             * @return bool
             */
            protected function can($permissionKey, $context = null): bool
            {
                return $context !== 2;
            }
        };
    }

    /**
     * @return void
     */
    public function testPlaceOrderSplit(): void
    {
        $uuid = 'a24f66c0-6a41-4a68-8d21-e3e80fba17b3';
        $customerReference = 'PS-C--1';
        $companyUserReference = 'PS-CU--1';
        $orderReferences = ['PS-000001'];

        $this->checkoutRequestValidatorInterfaceMock->expects($this->atLeastOnce())
            ->method('validateCheckoutRequest')
            ->willReturn($this->restErrorCollectionTransferMock);

        $this->restErrorCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getRestErrors')
            ->willReturn(new ArrayObject([]));

        $this->restCheckoutRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('getIdCart')
            ->willReturn($uuid);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getRestUser')
            ->willReturn($this->restUserTransferMock);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($customerReference);

        $this->cartsRestApiClientMock->expects($this->atLeastOnce())
            ->method('findQuoteByUuid')
            ->with($this->callback(
                static function (QuoteTransfer $quoteTransfer) use ($uuid, $customerReference) {
                    return $quoteTransfer->getUuid() === $uuid
                        && $quoteTransfer->getCustomerReference() === $customerReference
                        && $quoteTransfer->getCustomer()->getCustomerReference() === $customerReference;
                }
            ))->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($companyUserReference);

        $this->companyUserReferenceClientMock->expects($this->atLeastOnce())
            ->method('findCompanyUserByCompanyUserReference')
            ->with($this->callback(
                static function (CompanyUserTransfer $companyUserTransfer) use ($companyUserReference) {
                    return $companyUserReference === $companyUserTransfer->getCompanyUserReference();
                }
            ))->willReturn($this->companyUserResponseTransferMock);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->companyUserTransferMock->expects($this->atLeastOnce())
            ->method('getFkCompany')
            ->willReturn(1);

        $this->checkoutRequestAttributesExpanderInterfaceMock->expects($this->atLeastOnce())
            ->method('expandCheckoutRequestAttributes')
            ->with($this->restRequestMock, $this->restCheckoutRequestAttributesTransferMock)
            ->willReturn($this->restCheckoutRequestAttributesTransferMock);

        $this->checkoutRestApiClientMock->expects($this->atLeastOnce())
            ->method('placeOrderSplit')
            ->with($this->restCheckoutRequestAttributesTransferMock)
            ->willReturn($this->restCheckoutMultipleResponseTransferMock);

        $this->restCheckoutMultipleResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccess')
            ->willReturn(true);

        $this->restCheckoutMultipleResponseTransferMock->expects($this->atLeastOnce())
            ->method('getOrderReferences')
            ->willReturn($orderReferences);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResource')
            ->with(
                $this->callback(static function (string $type) {
                    return $type === CheckoutRestApiConfig::RESOURCE_CHECKOUT;
                }),
                $this->callback(static function (?string $id) {
                    return $id === null;
                }),
                $this->callback(static function (?AbstractTransfer $attributeTransfer) use ($orderReferences) {
                    return $attributeTransfer instanceof RestCheckoutMultipleResponseAttributesTransfer
                    && $attributeTransfer->getOrderReferences() === $orderReferences;
                })
            )->willReturn($this->restResourceMock);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseMock);

        $this->restResponseMock->expects($this->atLeastOnce())
            ->method('addResource')
            ->willReturn($this->restResponseMock);

        $this->assertEquals(
            $this->restResponseMock,
            $this->checkoutProcessor->placeOrderSplit(
                $this->restRequestMock,
                $this->restCheckoutRequestAttributesTransferMock
            )
        );
    }

    /**
     * @return void
     */
    public function testPlaceOrderSplitOrderPlaceOrderFailedException(): void
    {
        $uuid = 'a24f66c0-6a41-4a68-8d21-e3e80fba17b3';
        $customerReference = 'PS-C--1';
        $companyUserReference = 'PS-CU--1';
        $orderReferences = ['PS-000001'];

        $this->checkoutRequestValidatorInterfaceMock->expects($this->atLeastOnce())
            ->method('validateCheckoutRequest')
            ->willReturn($this->restErrorCollectionTransferMock);

        $this->restErrorCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getRestErrors')
            ->willReturn(new ArrayObject([]));

        $this->restCheckoutRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('getIdCart')
            ->willReturn($uuid);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getRestUser')
            ->willReturn($this->restUserTransferMock);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($customerReference);

        $this->cartsRestApiClientMock->expects($this->atLeastOnce())
            ->method('findQuoteByUuid')
            ->with($this->callback(
                static function (QuoteTransfer $quoteTransfer) use ($uuid, $customerReference) {
                    return $quoteTransfer->getUuid() === $uuid
                        && $quoteTransfer->getCustomerReference() === $customerReference
                        && $quoteTransfer->getCustomer()->getCustomerReference() === $customerReference;
                }
            ))->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($companyUserReference);

        $this->companyUserReferenceClientMock->expects($this->atLeastOnce())
            ->method('findCompanyUserByCompanyUserReference')
            ->with($this->callback(
                static function (CompanyUserTransfer $companyUserTransfer) use ($companyUserReference) {
                    return $companyUserReference === $companyUserTransfer->getCompanyUserReference();
                }
            ))->willReturn($this->companyUserResponseTransferMock);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->companyUserTransferMock->expects($this->atLeastOnce())
            ->method('getFkCompany')
            ->willReturn(1);

        $this->checkoutRequestAttributesExpanderInterfaceMock->expects($this->atLeastOnce())
            ->method('expandCheckoutRequestAttributes')
            ->with($this->restRequestMock, $this->restCheckoutRequestAttributesTransferMock)
            ->willReturn($this->restCheckoutRequestAttributesTransferMock);

        $this->checkoutRestApiClientMock->expects($this->atLeastOnce())
            ->method('placeOrderSplit')
            ->with($this->restCheckoutRequestAttributesTransferMock)
            ->willReturn($this->restCheckoutMultipleResponseTransferMock);

        $this->restCheckoutMultipleResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccess')
            ->willReturn(false);

        $this->restCheckoutMultipleResponseTransferMock->expects($this->atLeastOnce())
            ->method('getErrors')
            ->willReturn($this->restCheckoutErrorTransfers);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getMetadata')
            ->willReturn($this->metadataInterfaceMock);

        $this->metadataInterfaceMock->expects($this->atLeastOnce())
            ->method('getLocale')
            ->willReturn('locale');

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseMock);

        $this->restCheckoutErrorMapperInterfaceMock->expects($this->atLeastOnce())
            ->method('mapLocalizedRestCheckoutErrorTransferToRestErrorTransfer')
            ->willReturn($this->restErrorMessageTransferMock);

        $this->restResponseMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->checkoutProcessor->placeOrderSplit($this->restRequestMock, $this->restCheckoutRequestAttributesTransferMock));
    }

    /**
     * @return void
     */
    public function testPlaceOrderSplitValidationError(): void
    {
        $this->checkoutRequestValidatorInterfaceMock->expects($this->atLeastOnce())
            ->method('validateCheckoutRequest')
            ->willReturn($this->restErrorCollectionTransferMock);

        $this->restErrorCollectionTransferMock->expects($this->atLeast(2))
            ->method('getRestErrors')
            ->willReturn($this->restErrorMessageTransfers);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseMock);

        $this->restResponseMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->checkoutProcessor->placeOrderSplit($this->restRequestMock, $this->restCheckoutRequestAttributesTransferMock));
    }
}
