<?php

declare(strict_types=1);

namespace App\Entity;

final class Payment
{
    public function __construct(
        public int $id,
        public string $status,
    ) {
    }
}
