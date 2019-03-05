<?php

declare(strict_types=1);

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

use Generated\Shared\Transfer\QuoteTransfer;

interface QuoteCreatorByDeliveryDateInterface
{
    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer[]
     */
    public function getGeneratedQuotes(): array;

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getOriginalQuote(): QuoteTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @return void
     */
    public function splitAndCreateQuotesByDeliveryDate(QuoteTransfer $originalQuoteTransfer): void;
}
