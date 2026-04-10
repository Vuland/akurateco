<?php

declare(strict_types=1);

namespace App\Enum;

enum GatewayTransactionStatus: string
{
    case Settled = 'SETTLED';

    case Declined = 'DECLINED';

    case Redirect = 'REDIRECT';

    case ThreeDs = '3DS';

    case Prepare = 'PREPARE';

    case Pending = 'PENDING';

    case Waiting = 'WAITING';

    case Fail = 'FAIL';

    public function isPending(): bool
    {
        return $this === self::Pending
            || $this === self::Prepare
            || $this === self::ThreeDs
            || $this === self::Redirect;
    }
}
