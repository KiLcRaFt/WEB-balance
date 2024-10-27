<?php
require __DIR__ . '/vendor/autoload.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

$paypal = new ApiContext(
    new OAuthTokenCredential(
        'YOUR_PAYPAL_CLIENT_ID',
        'YOUR_PAYPAL_SECRET'
    )
);

$paypal->setConfig(
array(
'mode' => 'sandbox', // or 'live' for production
'log.LogEnabled' => true,
'log.FileName' => '../PayPal.log',
'log.LogLevel' => 'DEBUG', // PLEASE USE 'INFO' level for production
'cache.enabled' => true,
)
);
?>