<?php

declare(strict_types=1);

namespace App\DTO;

use Webmozart\Assert\Assert;

use function sprintf;

final class PaymentRequest
{
    /** @var list<string> */
    private const REQUIRED_RAW_KEYS = [
        'orderId',
        'amount',
        'currency',
        'description',
        'cardNumber',
        'cardExpMonth',
        'cardExpYear',
        'cardCvv2',
        'payerZip',
        'payerEmail',
    ];

    public function __construct(
        public readonly string $orderId,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $description,
        public readonly string $channelId,
        public readonly string $reqToken,
        public readonly string $cardToken,
        public readonly string $cardNumber,
        public readonly string $cardExpMonth,
        public readonly string $cardExpYear,
        public readonly string $cardCvv2,
        public readonly string $digitalWallet,
        public readonly string $paymentToken,
        public readonly string $payerFirstName,
        public readonly string $payerLastName,
        public readonly string $payerMiddleName,
        public readonly string $payerBirthDate,
        public readonly string $payerAddress,
        public readonly string $payerAddress2,
        public readonly string $payerHouseNumber,
        public readonly string $payerPhoneCountryCode,
        public readonly string $payerCountry,
        public readonly string $payerState,
        public readonly string $payerCity,
        public readonly string $payerDistrict,
        public readonly string $payerZip,
        public readonly string $payerEmail,
        public readonly string $payerPhone,
        public readonly string $payerIp,
        public readonly string $termUrl3ds,
        public readonly string $termUrlTarget,
        public readonly string $recurringInit,
        public readonly string $scheduleId,
        public readonly string $auth,
        public readonly array $parameters,
        public readonly array $customData,
    ) {
    }

    //TODO: maybe some hydrator?
    public static function fromRawData(array $rawData): self
    {
        // TODO: Validation can be improved
        foreach (self::REQUIRED_RAW_KEYS as $key) {
            Assert::keyExists($rawData, $key, sprintf('Missing "%s" in payment raw data.', $key));
        }

        return new self(
            orderId: $rawData['orderId'],
            amount: $rawData['amount'],
            currency: $rawData['currency'],
            description: $rawData['description'],
            channelId: $rawData['channelId'] ?? '',
            reqToken: $rawData['reqToken'] ?? '',
            cardToken: $rawData['cardToken'] ?? '',
            cardNumber: $rawData['cardNumber'],
            cardExpMonth: $rawData['cardExpMonth'],
            cardExpYear: $rawData['cardExpYear'],
            cardCvv2: $rawData['cardCvv2'],
            digitalWallet: $rawData['digitalWallet'] ?? '',
            paymentToken: $rawData['paymentToken'] ?? '',
            payerFirstName: $rawData['payerFirstName'] ?? '',
            payerLastName: $rawData['payerLastName'] ?? '',
            payerMiddleName: $rawData['payerMiddleName'] ?? '',
            payerBirthDate: $rawData['payerBirthDate'] ?? '',
            payerAddress: $rawData['payerAddress'] ?? '',
            payerAddress2: $rawData['payerAddress2'] ?? '',
            payerHouseNumber: $rawData['payerHouseNumber'] ?? '',
            payerPhoneCountryCode: $rawData['payerPhoneCountryCode'] ?? '',
            payerCountry: $rawData['payerCountry'] ?? '',
            payerState: $rawData['payerState'] ?? '',
            payerCity: $rawData['payerCity'] ?? '',
            payerDistrict: $rawData['payerDistrict'] ?? '',
            payerZip: $rawData['payerZip'],
            payerEmail: $rawData['payerEmail'],
            payerPhone: $rawData['payerPhone'] ?? '',
            payerIp: $rawData['payerIp'] ?? '',
            termUrl3ds: $rawData['termUrl3ds'] ?? '',
            termUrlTarget: $rawData['termUrlTarget'] ?? '',
            recurringInit: $rawData['recurringInit'] ?? '',
            scheduleId: $rawData['scheduleId'] ?? '',
            auth: $rawData['auth'] ?? '',
            parameters: $rawData['parameters'] ?? [],
            customData: $rawData['customData'] ?? [],
        );
    }
}
