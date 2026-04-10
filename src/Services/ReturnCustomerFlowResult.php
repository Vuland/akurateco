<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ProcessingResult;
use Webmozart\Assert\Assert;

final class ReturnCustomerFlowResult
{
    public function __construct(
        public readonly string $replyBody,
        public readonly ?ProcessingResult $processingResult = null,
    ) {
        Assert::inArray($replyBody, ['OK', 'ERROR'], 'replyBody must be OK or ERROR.');
    }
}
