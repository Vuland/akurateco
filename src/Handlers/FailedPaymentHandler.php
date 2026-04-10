<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DTO\ProcessingResult;
use App\DTO\SaleResponse;
use App\Entity\Payment;
use App\Enum\GatewayResult;
use App\Enum\PaymentStatus;

final class FailedPaymentHandler implements PaymentHandlerInterface
{
    public function canHandle(SaleResponse $gatewayResponse): bool
    {
        return GatewayResult::from($gatewayResponse->result)->isFailure();
    }

    public function handle(Payment $payment, SaleResponse $gatewayResponse): ProcessingResult
    {
        $payment->status = PaymentStatus::Failed->value;
        $reason = $gatewayResponse->declineReason ?? $gatewayResponse->errorMessage;

        return new ProcessingResult(
            status: PaymentStatus::Failed,
            message: $reason !== '' ? $reason : 'Payment declined by unknown reason',
            rawResponse: $gatewayResponse->raw,
        );
    }
}

