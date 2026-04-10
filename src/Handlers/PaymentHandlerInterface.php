<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DTO\ProcessingResult;
use App\DTO\SaleResponse;
use App\Entity\Payment;

interface PaymentHandlerInterface
{
    public function canHandle(SaleResponse $gatewayResponse): bool;

    public function handle(Payment $payment, SaleResponse $gatewayResponse): ProcessingResult;
}

