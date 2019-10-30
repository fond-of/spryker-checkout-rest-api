<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessorInterface;
use FondOfSpryker\Zed\CheckoutRestApi\CheckoutRestApiConfig;
use FondOfSpryker\Zed\CheckoutRestApi\CheckoutRestApiDependencyProvider as CheckoutRestApiCheckoutRestApiDependencyProvider;
use Spryker\Zed\CheckoutRestApi\CheckoutRestApiDependencyProvider;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCalculationFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCartFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCartsRestApiFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCheckoutFacadeInterface;
use Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToQuoteFacadeInterface;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\MultiCart\Business\MultiCartFacadeInterface;
use Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface;
use Spryker\Zed\Quote\Business\QuoteFacadeInterface;

class CheckoutRestApiBusinessFactoryTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CheckoutRestApi\Business\CheckoutRestApiBusinessFactory
     */
    protected $checkoutRestApiBusinessFactory;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CheckoutRestApi\CheckoutRestApiConfig
     */
    protected $checkoutRestApiConfigMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Kernel\Container
     */
    protected $containerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCartsRestApiFacadeInterface
     */
    protected $checkoutRestApiToCartsRestApiFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCartFacadeInterface
     */
    protected $checkoutRestApiToCartFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCheckoutFacadeInterface
     */
    protected $checkoutRestApiToCheckoutFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToQuoteFacadeInterface
     */
    protected $checkoutRestApiToQuoteFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CheckoutRestApi\Dependency\Facade\CheckoutRestApiToCalculationFacadeInterface
     */
    protected $checkoutRestApiToCalculationFacadeInterfaceMock;

    /**
     * @var array
     */
    protected $quoteMapperPlugins;

    /**
     * @var array
     */
    private $checkoutDataValidatorPlugins;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface
     */
    private $persistentCartFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\MultiCart\Business\MultiCartFacadeInterface
     */
    private $multiCartFacadeInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Quote\Business\QuoteFacadeInterface
     */
    private $quoteFacadeInterfaceMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->checkoutRestApiConfigMock = $this->getMockBuilder(CheckoutRestApiConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToCartsRestApiFacadeInterfaceMock = $this->getMockBuilder(CheckoutRestApiToCartsRestApiFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToCartFacadeInterfaceMock = $this->getMockBuilder(CheckoutRestApiToCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToCheckoutFacadeInterfaceMock = $this->getMockBuilder(CheckoutRestApiToCheckoutFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToQuoteFacadeInterfaceMock = $this->getMockBuilder(CheckoutRestApiToQuoteFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiToCalculationFacadeInterfaceMock = $this->getMockBuilder(CheckoutRestApiToCalculationFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteMapperPlugins = [

        ];

        $this->checkoutDataValidatorPlugins = [

        ];

        $this->persistentCartFacadeInterfaceMock = $this->getMockBuilder(PersistentCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->multiCartFacadeInterfaceMock = $this->getMockBuilder(MultiCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteFacadeInterfaceMock = $this->getMockBuilder(QuoteFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutRestApiBusinessFactory = new CheckoutRestApiBusinessFactory();
        $this->checkoutRestApiBusinessFactory->setConfig($this->checkoutRestApiConfigMock);
        $this->checkoutRestApiBusinessFactory->setContainer($this->containerMock);
    }

    /**
     * @return void
     */
    public function testCreateFondOfPlaceOrderProcessor(): void
    {
        $this->containerMock->expects($this->atLeast(1))
            ->method('has')
            ->willReturn(true);

        $this->containerMock->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnOnConsecutiveCalls([
                CheckoutRestApiDependencyProvider::FACADE_CARTS_REST_API,
                CheckoutRestApiDependencyProvider::FACADE_CART,
                CheckoutRestApiDependencyProvider::FACADE_CHECKOUT,
                CheckoutRestApiDependencyProvider::FACADE_QUOTE,
                CheckoutRestApiDependencyProvider::FACADE_CALCULATION,
                CheckoutRestApiDependencyProvider::PLUGINS_QUOTE_MAPPER,
                CheckoutRestApiDependencyProvider::PLUGINS_CHECKOUT_DATA_VALIDATOR,
                CheckoutRestApiCheckoutRestApiDependencyProvider::FACADE_PERSISTENT_CART,
                CheckoutRestApiCheckoutRestApiDependencyProvider::FACADE_MULTI_CART,
                CheckoutRestApiCheckoutRestApiDependencyProvider::FACADE_QUOTE_REAL,
            ])->willReturnOnConsecutiveCalls(
            $this->checkoutRestApiToCartsRestApiFacadeInterfaceMock,
            $this->checkoutRestApiToCartFacadeInterfaceMock,
            $this->checkoutRestApiToCheckoutFacadeInterfaceMock,
            $this->checkoutRestApiToQuoteFacadeInterfaceMock,
            $this->checkoutRestApiToCalculationFacadeInterfaceMock,
            $this->quoteMapperPlugins,
            $this->checkoutDataValidatorPlugins,
            $this->persistentCartFacadeInterfaceMock,
            $this->multiCartFacadeInterfaceMock,
            $this->quoteFacadeInterfaceMock,
        );

        $this->assertInstanceOf(PlaceOrderProcessorInterface::class, $this->checkoutRestApiBusinessFactory->createFondOfPlaceOrderProcessor());
    }
}
