<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\PaymentRequest;
use App\DTO\SaleResponse;
use GuzzleHttp\ClientInterface;
use RuntimeException;
use Throwable;

use function array_filter;
use function json_decode;
use function md5;
use function number_format;
use function strrev;
use function strtoupper;
use function substr;

final class PaymentGatewayClient implements PaymentGatewayClientInterface
{
    private const ACTION_SALE = 'SALE';

    public function __construct(
        private readonly ClientInterface $http,
        private readonly string $apiUrl,
        private readonly string $clientKey,
        private readonly string $password,
    ) {
    }

    public function sale(PaymentRequest $request): SaleResponse
    {
        $formParams = [
            'action' => self::ACTION_SALE,
            'client_key' => $this->clientKey,
            'channel_id' => $request->channelId,
            'order_id' => $request->orderId,
            'order_amount' => number_format($request->amount, 2, '.', ''),
            'order_currency' => $request->currency,
            'order_description' => $request->description,
            'req_token' => $request->reqToken,
            'card_token' => $request->cardToken,
            'card_number' => $request->cardNumber,
            'card_exp_month' => $request->cardExpMonth,
            'card_exp_year' => $request->cardExpYear,
            'card_cvv2' => $request->cardCvv2,
            'digital_wallet' => $request->digitalWallet,
            'payment_token' => $request->paymentToken,
            'payer_first_name' => $request->payerFirstName,
            'payer_last_name' => $request->payerLastName,
            'payer_middle_name' => $request->payerMiddleName,
            'payer_birth_date' => $request->payerBirthDate,
            'payer_address' => $request->payerAddress,
            'payer_address2' => $request->payerAddress2,
            'payer_house_number' => $request->payerHouseNumber,
            'payer_phone_country_code' => $request->payerPhoneCountryCode,
            'payer_country' => $request->payerCountry,
            'payer_state' => $request->payerState,
            'payer_city' => $request->payerCity,
            'payer_district' => $request->payerDistrict,
            'payer_zip' => $request->payerZip,
            'payer_email' => $request->payerEmail,
            'payer_phone' => $request->payerPhone,
            'payer_ip' => $request->payerIp,
            'term_url_3ds' => $request->termUrl3ds,
            'term_url_target' => $request->termUrlTarget,
            'recurring_init' => $request->recurringInit,
            'schedule_id' => $request->scheduleId,
            'auth' => $request->auth,
        ];

        $signature = $request->cardToken;
        if ($signature === '') {
            $signature = substr($request->cardNumber, 0, 6) . substr($request->cardNumber, -4);
        }
        $formParams['hash'] = md5(strtoupper(strrev($request->payerEmail) . $this->password . strrev($signature)));

        $formParams = array_filter($formParams, static fn (mixed $v): bool => $v !== '' && $v !== null);

        //TODO: implement parameters and customData field behaviours

        try {
            $response = $this->http->request('POST', $this->apiUrl, [
                'form_params' => $formParams,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
                'timeout' => 30,
            ]);
        } catch (Throwable $e) {
            throw new RuntimeException('Gateway request failed: ' . $e->getMessage(), 0, $e);
        }

        $body = $response->getBody();
        $decoded = json_decode((string)$body, true, 512, JSON_THROW_ON_ERROR);

        return SaleResponse::fromArray($decoded);
    }
}
