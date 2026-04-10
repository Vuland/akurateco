<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Entity\Payment;
use App\Enum\PaymentStatus;
use App\Handlers\FailedPaymentHandler;
use App\Handlers\PendingPaymentHandler;
use App\Handlers\RedirectPaymentHandler;
use App\Handlers\SuccessPaymentHandler;
use App\PaymentProcessor;
use App\Registry\PaymentHandlerRegistry;

$registry = new PaymentHandlerRegistry();
$registry->registerHandler(new SuccessPaymentHandler());
$registry->registerHandler(new PendingPaymentHandler());
$registry->registerHandler(new FailedPaymentHandler());
$registry->registerHandler(new RedirectPaymentHandler());

$processor = new PaymentProcessor($registry);
$paymentFromDb = new Payment(1, PaymentStatus::Prepared->value);

$rawData = [
    'orderId' => '12345',
    'cardNumber' => '4111111111111111',
    'cardExpMonth' => '12',
    'cardExpYear' => '2028',
    'cardCvv2' => '123',
    'amount' => 1500.50,
    'currency' => 'USD',
    'description' => 'Example purchase',
    'channelId' => '',
    'reqToken' => '',
    'cardToken' => '',
    'digitalWallet' => '',
    'paymentToken' => '',
    'payerFirstName' => 'Jane',
    'payerLastName' => 'Doe',
    'payerMiddleName' => '',
    'payerBirthDate' => '1990-01-15',
    'payerAddress' => '1 Main St',
    'payerAddress2' => '',
    'payerHouseNumber' => '1',
    'payerPhoneCountryCode' => '',
    'payerCountry' => 'US',
    'payerState' => 'NY',
    'payerCity' => 'New York',
    'payerDistrict' => 'NY',
    'payerZip' => '10001',
    'payerEmail' => 'customer@example.com',
    'payerPhone' => '+12125551234',
    'payerIp' => '203.0.113.10',
    'termUrl3ds' => 'https://example.com/3ds-return',
    'termUrlTarget' => '',
    'recurringInit' => '',
    'scheduleId' => '',
    'auth' => '',
    'parameters' => [],
    'customData' => [],
];

$result = $processor->process($paymentFromDb, $rawData);

echo $result->status->value . PHP_EOL;
echo $result->message . PHP_EOL;

if ($result->status === PaymentStatus::Redirect && $result->redirectData !== null) {
    echo 'Redirect URL: ' . $result->redirectData['url'] . PHP_EOL;
    echo 'Redirect method: ' . $result->redirectData['method'] . PHP_EOL;
    echo 'Redirect params: ' . json_encode($result->redirectData['params'], JSON_THROW_ON_ERROR) . PHP_EOL;
}
