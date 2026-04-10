<?php

declare(strict_types=1);

namespace App\Registry;

use App\DTO\SaleResponse;
use App\Handlers\PaymentHandlerInterface;
use RuntimeException;

final class PaymentHandlerRegistry
{
    /** @var list<PaymentHandlerInterface> */
    private array $handlers = [];

    public function registerHandler(PaymentHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function getHandler(SaleResponse $gatewayResponse): PaymentHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($gatewayResponse)) {
                return $handler;
            }
        }

        throw new RuntimeException('No handler registered for this gateway response.');
    }
}
