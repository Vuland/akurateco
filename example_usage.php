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
    'cardNumber' => '4444555566667777',
    'cardExpMonth' => '01',
    'cardExpYear' => '2027',
    'cardCvv2' => '123',
    'amount' => 1500.50,
    'currency' => 'USD',
    'description' => 'Test payment',
    'channelId' => '',
    'reqToken' => '',
    'cardToken' => '',
    'digitalWallet' => '',
    'paymentToken' => '',
    'payerFirstName' => 'Dmytro',
    'payerLastName' => 'Rudakov',
    'payerMiddleName' => '',
    'payerBirthDate' => '',
    'payerAddress' => 'Sadova',
    'payerAddress2' => '',
    'payerHouseNumber' => '',
    'payerPhoneCountryCode' => '',
    'payerCountry' => 'UA',
    'payerState' => '',
    'payerCity' => 'Kyiv',
    'payerDistrict' => '',
    'payerZip' => '01001',
    'payerEmail' => 'customer@example.com',
    'payerPhone' => '+380123121212',
    'payerIp' => '123.123.123.123',
    'termUrl3ds' => 'https://localhost/return.php',
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
