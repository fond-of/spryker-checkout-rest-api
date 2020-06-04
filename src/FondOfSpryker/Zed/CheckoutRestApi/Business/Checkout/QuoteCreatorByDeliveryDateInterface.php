<?php

declare(strict_types = 1);

namespace FondOfSpryker\Zed\CheckoutRestApi\Business\Checkout;

use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface QuoteCreatorByDeliveryDateInterface
{
    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getOriginalQuoteTransfer(): QuoteTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $originalQuoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    public function createAndPersistChildQuotesByDeliveryDate(QuoteTransfer $originalQuoteTransfer): QuoteCollectionTransfer;
}
