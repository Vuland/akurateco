<?php

declare(strict_types=1);

namespace App;

use App\DTO\PaymentRequest;
use App\DTO\ProcessingResult;
use App\Entity\Payment;
use App\Registry\PaymentHandlerRegistry;
use App\Services\PaymentGatewayClient;
use App\Services\PaymentGatewayClientInterface;
use GuzzleHttp\Client;
use RuntimeException;

use function getenv;

final class PaymentProcessor
{
    private readonly PaymentHandlerRegistry $registry;

    private readonly PaymentGatewayClientInterface $gateway;

    public function __construct(
        PaymentHandlerRegistry $registry,
        ?PaymentGatewayClientInterface $gateway = null,
    ) {
        $this->registry = $registry;
        $this->gateway = $gateway ?? $this->buildGatewayFromEnv();
    }

    public function process(Payment $paymentEntity, array $rawData): ProcessingResult
    {
        $request = PaymentRequest::fromRawData($rawData);
        $gatewayResponse = $this->gateway->sale($request);

        return $this->registry->getHandler($gatewayResponse)->handle($paymentEntity, $gatewayResponse);
    }

    private function buildGatewayFromEnv(): PaymentGatewayClientInterface
    {
        $apiUrl = getenv('RAFINITA_API_URL');
        $clientKey = getenv('RAFINITA_PUBLIC_KEY');
        $password = getenv('RAFINITA_PASS');

        if ($apiUrl === false || $clientKey === false || $password === false) {
            throw new RuntimeException('Required env vars is missing');
        }

        return new PaymentGatewayClient(
            http: new Client(),
            apiUrl: $apiUrl,
            clientKey: $clientKey,
            password: $password,
        );
    }
}
