<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CheckoutRestApi;

use Spryker\Glue\CheckoutRestApi\CheckoutRestApiConfig as SprykerCheckoutRestApiConfig;

class CheckoutRestApiConfig extends SprykerCheckoutRestApiConfig
{
    public const RESPONSE_CODE_ACCESS_DENIED = '1110';
    public const RESPONSE_MESSAGE_ACCESS_DENIED = 'Access Denied';
}
