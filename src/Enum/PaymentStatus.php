<?php

declare(strict_types=1);

namespace App\Enum;

enum PaymentStatus: string
{
    case Prepared = 'prepared';
    case Success = 'success';
    case Failed = 'failed';
    case Waiting = 'waiting';
    case Redirect = 'redirect';
}

