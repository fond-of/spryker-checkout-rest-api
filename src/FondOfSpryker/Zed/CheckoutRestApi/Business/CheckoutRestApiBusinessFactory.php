<?php

namespace FondOfSpryker\Zed\CheckoutRestApi\Business;

use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessor;
use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\PlaceOrderProcessorInterface;
use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDate;
use FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDateInterface;
use Spryker\Zed\CheckoutRestApi\Business\CheckoutRestApiBusinessFactory as SprykerCheckoutRestApiBusinessFactory;
use FondOfSpryker\Zed\CheckoutRestApi\CheckoutRestApiDependencyProvider;
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
            $this->createQuoteCreatorByDeliveryDate()
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout\QuoteCreatorByDeliveryDate
     */
    public function createQuoteCreatorByDeliveryDate(): QuoteCreatorByDeliveryDateInterface
    {
        return new QuoteCreatorByDeliveryDate(
            $this->getQuoteFacadeReal()
        );
    }

    /**
     * @return \Spryker\Zed\Quote\Business\QuoteFacadeInterface
     */
    public function getQuoteFacadeReal(): QuoteFacadeInterface
    {
        return $this->getProvidedDependency(CheckoutRestApiDependencyProvider::FACADE_QUOTE_REAL);
    }
}
