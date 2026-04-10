<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\PaymentStatus;
use Webmozart\Assert\Assert;

final class ProcessingResult
{
    public function __construct(
        public PaymentStatus $status,
        public string $message,
        public array $rawResponse = [],
        public ?array $redirectData = null,
    ) {
        // TODO: Validation can be improved
        if ($redirectData !== null) {
            Assert::keyExists($redirectData, 'url');
            Assert::keyExists($redirectData, 'method');
        }
    }
}
