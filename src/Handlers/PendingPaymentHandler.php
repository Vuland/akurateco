<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DTO\ProcessingResult;
use App\DTO\SaleResponse;
use App\Entity\Payment;
use App\Enum\GatewayResult;
use App\Enum\GatewayTransactionStatus;
use App\Enum\PaymentStatus;

final class PendingPaymentHandler implements PaymentHandlerInterface
{
    public function canHandle(SaleResponse $gatewayResponse): bool
    {
        $result = GatewayResult::from($gatewayResponse->result);

        if ($result === GatewayResult::Undefined) {
            return true;
        }

        if ($result !== GatewayResult::Success) {
            return false;
        }

        return GatewayTransactionStatus::from($gatewayResponse->status)->isPending();
    }

    public function handle(Payment $payment, SaleResponse $gatewayResponse): ProcessingResult
    {
        $payment->status = PaymentStatus::Waiting->value;

        return new ProcessingResult(
            status: PaymentStatus::Waiting,
            message: 'Pending',
            rawResponse: $gatewayResponse->raw,
        );
    }
}

