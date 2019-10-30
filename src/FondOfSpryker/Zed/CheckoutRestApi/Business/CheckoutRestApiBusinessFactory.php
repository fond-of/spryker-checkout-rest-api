<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business;

use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessor;
use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessorInterface;
use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDate;
use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDateInterface;
use FondOfSpryker\Zed\CheckoutRestApi\CheckoutRestApiDependencyProvider;
use Spryker\Zed\CheckoutRestApi\Business\CheckoutRestApiBusinessFactory as SprykerCheckoutRestApiBusinessFactory;
use Spryker\Zed\MultiCart\Business\MultiCartFacadeInterface;
use Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface;
use Spryker\Zed\Quote\Business\QuoteFacadeInterface;

/**
 * @method \FondOfSpryker\Zed\CheckoutRestApi\CheckoutRestApiConfig getConfig()
 */
class CheckoutRestApiBusinessFactory extends SprykerCheckoutRestApiBusinessFactory
{
    /**
     * @return \FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessorInterface
     */
    public function createFondOfPlaceOrderProcessor(): PlaceOrderProcessorInterface
    {
        return new PlaceOrderProcessor(
            $this->createQuoteReader(),
            $this->getCartFacade(),
            $this->getCheckoutFacade(),
            $this->getQuoteFacade(),
            $this->getCalculationFacade(),
            $this->getQuoteMapperPlugins(),
            $this->getCheckoutDataValidatorPlugins(),
            $this->createQuoteCreatorByDeliveryDate(),
            $this->getQuoteFacadeReal()
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDateInterface
     */
    public function createQuoteCreatorByDeliveryDate(): QuoteCreatorByDeliveryDateInterface
    {
        return new QuoteCreatorByDeliveryDate(
            $this->getPersistentCartFacade(),
            $this->getMultiCartFacade()
        );
    }

    /**
     * @throws
     *
     * @return \Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface
     */
    public function getPersistentCartFacade(): PersistentCartFacadeInterface
    {
        return $this->getProvidedDependency(CheckoutRestApiDependencyProvider::FACADE_PERSISTENT_CART);
    }

    /**
     * @throws
     *
     * @return \Spryker\Zed\MultiCart\Business\MultiCartFacadeInterface
     */
    public function getMultiCartFacade(): MultiCartFacadeInterface
    {
        return $this->getProvidedDependency(CheckoutRestApiDependencyProvider::FACADE_MULTI_CART);
    }

    /**
     * @throws
     *
     * @return \Spryker\Zed\Quote\Business\QuoteFacadeInterface
     */
    public function getQuoteFacadeReal(): QuoteFacadeInterface
    {
        return $this->getProvidedDependency(CheckoutRestApiDependencyProvider::FACADE_QUOTE_REAL);
    }
}
