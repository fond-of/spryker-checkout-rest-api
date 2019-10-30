<?php

namespace FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface;
use Generated\Shared\Transfer\RestCheckoutErrorTransfer;
use Generated\Shared\Transfer\RestCheckoutMultipleResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestErrorCollectionTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutResponseMapperInterface;
use Spryker\Glue\CheckoutRestApi\Processor\Error\RestCheckoutErrorMapperInterface;
use Spryker\Glue\CheckoutRestApi\Processor\RequestAttributesExpander\CheckoutRequestAttributesExpanderInterface;
use Spryker\Glue\CheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\MetadataInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CheckoutProcessorTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessor
     */
    protected $checkoutProcessor;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface
     */
    protected $checkoutRestApiClientInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilderInterfaceMock;

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
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface
     */
    protected $restRequestInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer
     */
    protected $restCheckoutRequestAttributesTransferMock;

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
    protected $restResponseInterfaceMock;

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
    private $restResourceInterfaceMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->checkoutRestApiClientInterfaceMock = $this->getMockBuilder(CheckoutRestApiClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceBuilderInterfaceMock = $this->getMockBuilder(RestResourceBuilderInterface::class)
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

        $this->restRequestInterfaceMock = $this->getMockBuilder(RestRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCheckoutRequestAttributesTransferMock = $this->getMockBuilder(RestCheckoutRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restErrorCollectionTransferMock = $this->getMockBuilder(RestErrorCollectionTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restErrorMessageTransferMock = $this->getMockBuilder(RestErrorMessageTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResponseInterfaceMock = $this->getMockBuilder(RestResponseInterface::class)
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

        $this->restResourceInterfaceMock = $this->getMockBuilder(RestResourceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutProcessor = new CheckoutProcessor(
            $this->checkoutRestApiClientInterfaceMock,
            $this->restResourceBuilderInterfaceMock,
            $this->checkoutRequestAttributesExpanderInterfaceMock,
            $this->checkoutRequestValidatorInterfaceMock,
            $this->restCheckoutErrorMapperInterfaceMock,
            $this->checkoutResponseMapperInterfaceMock
        );
    }

    /**
     * @return void
     */
    public function testPlaceOrderSplit(): void
    {
        $this->checkoutRequestValidatorInterfaceMock->expects($this->atLeastOnce())
            ->method('validateCheckoutRequest')
            ->willReturn($this->restErrorCollectionTransferMock);

        $this->restErrorCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getRestErrors')
            ->willReturn(new ArrayObject([]));

        $this->checkoutRequestAttributesExpanderInterfaceMock->expects($this->atLeastOnce())
            ->method('expandCheckoutRequestAttributes')
            ->willReturn($this->restCheckoutRequestAttributesTransferMock);

        $this->checkoutRestApiClientInterfaceMock->expects($this->atLeastOnce())
            ->method('placeOrderSplit')
            ->willReturn($this->restCheckoutMultipleResponseTransferMock);

        $this->restCheckoutMultipleResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccess')
            ->willReturn(true);

        $this->restCheckoutMultipleResponseTransferMock->expects($this->atLeastOnce())
            ->method('getOrderReferences')
            ->willReturn([]);

        $this->restResourceBuilderInterfaceMock->expects($this->atLeastOnce())
            ->method('createRestResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceBuilderInterfaceMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addResource')
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->checkoutProcessor->placeOrderSplit($this->restRequestInterfaceMock, $this->restCheckoutRequestAttributesTransferMock));
    }

    /**
     * @return void
     */
    public function testPlaceOrderSplitOrderPlaceOrderFailedException(): void
    {
        $this->checkoutRequestValidatorInterfaceMock->expects($this->atLeastOnce())
            ->method('validateCheckoutRequest')
            ->willReturn($this->restErrorCollectionTransferMock);

        $this->restErrorCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getRestErrors')
            ->willReturn(new ArrayObject([]));

        $this->checkoutRequestAttributesExpanderInterfaceMock->expects($this->atLeastOnce())
            ->method('expandCheckoutRequestAttributes')
            ->willReturn($this->restCheckoutRequestAttributesTransferMock);

        $this->checkoutRestApiClientInterfaceMock->expects($this->atLeastOnce())
            ->method('placeOrderSplit')
            ->willReturn($this->restCheckoutMultipleResponseTransferMock);

        $this->restCheckoutMultipleResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccess')
            ->willReturn(false);

        $this->restCheckoutMultipleResponseTransferMock->expects($this->atLeastOnce())
            ->method('getErrors')
            ->willReturn($this->restCheckoutErrorTransfers);

        $this->restRequestInterfaceMock->expects($this->atLeastOnce())
            ->method('getMetadata')
            ->willReturn($this->metadataInterfaceMock);

        $this->metadataInterfaceMock->expects($this->atLeastOnce())
            ->method('getLocale')
            ->willReturn("locale");

        $this->restResourceBuilderInterfaceMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->restCheckoutErrorMapperInterfaceMock->expects($this->atLeastOnce())
            ->method('mapLocalizedRestCheckoutErrorTransferToRestErrorTransfer')
            ->willReturn($this->restErrorMessageTransferMock);

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->checkoutProcessor->placeOrderSplit($this->restRequestInterfaceMock, $this->restCheckoutRequestAttributesTransferMock));
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

        $this->restResourceBuilderInterfaceMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->checkoutProcessor->placeOrderSplit($this->restRequestInterfaceMock, $this->restCheckoutRequestAttributesTransferMock));
    }
}
