<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DTO\ProcessingResult;
use App\DTO\SaleResponse;
use App\Entity\Payment;
use App\Enum\GatewayResult;
use App\Enum\GatewayTransactionStatus;
use App\Enum\PaymentStatus;

final class SuccessPaymentHandler implements PaymentHandlerInterface
{
    public function canHandle(SaleResponse $gatewayResponse): bool
    {
        if (GatewayResult::from($gatewayResponse->result) !== GatewayResult::Success) {
            return false;
        }

        return GatewayTransactionStatus::from($gatewayResponse->status) === GatewayTransactionStatus::Settled;
    }

    public function handle(Payment $payment, SaleResponse $gatewayResponse): ProcessingResult
    {
        $payment->status = PaymentStatus::Success->value;

        return new ProcessingResult(
            status: PaymentStatus::Success,
            message: 'Payment succeeded',
            rawResponse: $gatewayResponse->raw,
        );
    }
}

