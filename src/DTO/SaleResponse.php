<?php

declare(strict_types=1);

namespace App\DTO;

use Webmozart\Assert\Assert;

final class SaleResponse
{
    public function __construct(
        public readonly string $result,
        public readonly ?string $status,
        public readonly ?string $declineReason,
        public readonly ?string $errorMessage,
        public readonly ?string $redirectUrl,
        public readonly mixed $redirectParams,
        public readonly ?string $redirectMethod,
        public readonly array $raw,
    ) {
    }

    public static function fromArray(array $data): self
    {
        // TODO: Validation can be improved
        Assert::keyExists($data, 'result', 'Gateway response must contain result.');
        Assert::string($data['result'], 'Gateway response must be a string.');

        return new self(
            result: $data['result'],
            status: $data['status'] ?? null,
            declineReason: $data['decline_reason'] ?? null,
            errorMessage: $data['error_message'] ?? null,
            redirectUrl: $data['redirect_url'] ?? null,
            redirectParams: $data['redirect_params'] ?? null,
            redirectMethod: $data['redirect_method'] ?? null,
            raw: $data,
        );
    }
}
