<?php

declare(strict_types=1);

namespace Tests;

use App\DTO\SaleResponse;
use App\Entity\Payment;
use App\Enum\PaymentStatus;
use App\Handlers\FailedPaymentHandler;
use App\Handlers\PendingPaymentHandler;
use App\Handlers\RedirectPaymentHandler;
use App\Handlers\SuccessPaymentHandler;
use App\PaymentProcessor;
use App\Registry\PaymentHandlerRegistry;
use PHPUnit\Framework\TestCase;
use Tests\Mocks\MockGatewayClient;

final class PaymentProcessorTest extends TestCase
{
    public function testSuccessCase(): void
    {
        $processor = new PaymentProcessor(
            registry: $this->buildRegistry(),
            gateway: new MockGatewayClient($this->makeSaleResponse([
                'result' => 'SUCCESS',
                'status' => 'SETTLED',
            ])),
        );

        $payment = new Payment(1, PaymentStatus::Prepared->value);
        $result = $processor->process($payment, $this->mockRequiredData());

        self::assertSame(PaymentStatus::Success, $result->status);
        self::assertSame(PaymentStatus::Success->value, $payment->status);
    }

    public function testDeclinedCase(): void
    {
        $processor = new PaymentProcessor(
            registry: $this->buildRegistry(),
            gateway: new MockGatewayClient($this->makeSaleResponse([
                'result' => 'DECLINED',
                'decline_reason' => 'Not enough funds',
            ])),
        );

        $payment = new Payment(1, PaymentStatus::Prepared->value);
        $result = $processor->process($payment, $this->mockRequiredData());

        self::assertSame(PaymentStatus::Failed, $result->status);
        self::assertSame(PaymentStatus::Failed->value, $payment->status);
        self::assertSame('Not enough funds', $result->message);
    }

    public function testRedirectCase(): void
    {
        $processor = new PaymentProcessor(
            registry: $this->buildRegistry(),
            gateway: new MockGatewayClient($this->makeSaleResponse([
                'result' => 'REDIRECT',
                'status' => 'REDIRECT',
                'redirect_url' => 'https://example.com/rafinita',
                'redirect_method' => 'POST',
            ])),
        );

        $payment = new Payment(1, PaymentStatus::Prepared->value);
        $result = $processor->process($payment, $this->mockRequiredData());

        self::assertSame(PaymentStatus::Redirect, $result->status);
        self::assertSame(PaymentStatus::Redirect->value, $payment->status);
        self::assertNotNull($result->redirectData);
        self::assertSame('https://example.com/rafinita', $result->redirectData['url']);
    }

    public function testWaitingCase(): void
    {
        $processor = new PaymentProcessor(
            registry: $this->buildRegistry(),
            gateway: new MockGatewayClient($this->makeSaleResponse([
                'result' => 'SUCCESS',
                'status' => 'PENDING',
            ])),
        );

        $payment = new Payment(1, PaymentStatus::Prepared->value);
        $result = $processor->process($payment, $this->mockRequiredData());

        self::assertSame(PaymentStatus::Waiting, $result->status);
        self::assertSame(PaymentStatus::Waiting->value, $payment->status);
    }

    private function buildRegistry(): PaymentHandlerRegistry
    {
        $registry = new PaymentHandlerRegistry();
        $registry->registerHandler(new SuccessPaymentHandler());
        $registry->registerHandler(new PendingPaymentHandler());
        $registry->registerHandler(new FailedPaymentHandler());
        $registry->registerHandler(new RedirectPaymentHandler());

        return $registry;
    }

    private function makeSaleResponse(array $response): SaleResponse
    {
        return SaleResponse::fromArray($response);
    }

    private function mockRequiredData(): array
    {
        return [
            'orderId' => '12345',
            'cardNumber' => '4111111111111111',
            'cardExpMonth' => '01',
            'cardExpYear' => '2027',
            'cardCvv2' => '111',
            'amount' => 100.00,
            'currency' => 'UAH',
            'description' => 'Test payment',
            'payerFirstName' => 'Dmytro',
            'payerLastName' => 'Rudakov',
            'payerAddress' => 'Sadova',
            'payerCountry' => 'UA',
            'payerCity' => 'Kyiv',
            'payerZip' => '01001',
            'payerEmail' => 'wostr.pl.rd@gmail.com',
            'payerPhone' => '+380123121212',
            'payerIp' => '123.123.123.123',
            'termUrl3ds' => 'https://localhost/return.php',
        ];
    }
}
