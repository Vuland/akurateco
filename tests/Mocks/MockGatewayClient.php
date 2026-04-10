<?php

declare(strict_types=1);

namespace Tests\Mocks;

use App\DTO\PaymentRequest;
use App\DTO\SaleResponse;
use App\Services\PaymentGatewayClientInterface;

final class MockGatewayClient implements PaymentGatewayClientInterface
{
    public function __construct(
        private readonly SaleResponse $response,
    ) {
    }

    public function sale(PaymentRequest $request): SaleResponse
    {
        return $this->response;
    }
}
