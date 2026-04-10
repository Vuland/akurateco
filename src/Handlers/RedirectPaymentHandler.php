<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DTO\ProcessingResult;
use App\DTO\SaleResponse;
use App\Entity\Payment;
use App\Enum\GatewayResult;
use App\Enum\PaymentStatus;

final class RedirectPaymentHandler implements PaymentHandlerInterface
{
    public function canHandle(SaleResponse $gatewayResponse): bool
    {
        $result = GatewayResult::from($gatewayResponse->result);

        return $result === GatewayResult::Redirect;
    }

    public function handle(Payment $payment, SaleResponse $gatewayResponse): ProcessingResult
    {
        $payment->status = PaymentStatus::Redirect->value;

        $redirectData = [
            'url' => $gatewayResponse->redirectUrl,
            'method' => $gatewayResponse->redirectMethod,
            'params' => $gatewayResponse->redirectParams,
        ];

        return new ProcessingResult(
            status: PaymentStatus::Redirect,
            message: 'Redirect required',
            rawResponse: $gatewayResponse->raw,
            redirectData: $redirectData,
        );
    }
}

