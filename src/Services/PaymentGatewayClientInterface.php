<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\PaymentRequest;
use App\DTO\SaleResponse;

interface PaymentGatewayClientInterface
{
    public function sale(PaymentRequest $request): SaleResponse;
}

