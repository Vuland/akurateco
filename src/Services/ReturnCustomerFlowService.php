<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\SaleResponse;
use App\Entity\Payment;
use App\Registry\PaymentHandlerRegistry;

use function is_string;

final class ReturnCustomerFlowService
{
    public function __construct(
        private readonly PaymentHandlerRegistry $registry,
    ) {
    }

    public function handleNotification(Payment $paymentEntity, array $payload): ReturnCustomerFlowResult
    {
        if (!isset($payload['result']) || !is_string($payload['result']) || $payload['result'] === '') {
            return new ReturnCustomerFlowResult('ERROR');
        }

        $gatewayResponse = SaleResponse::fromArray($payload);
        $processing = $this->registry->getHandler($gatewayResponse)->handle($paymentEntity, $gatewayResponse);

        return new ReturnCustomerFlowResult('OK', $processing);
    }
}
