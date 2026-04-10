<?php

declare(strict_types=1);

namespace App\Enum;

enum GatewayResult: string
{
    case Success = 'SUCCESS';

    case Declined = 'DECLINED';

    case Redirect = 'REDIRECT';

    case Undefined = 'UNDEFINED';

    case Fail = 'FAIL';

    case Error = 'ERROR';

    public function isFailure(): bool
    {
        return $this === self::Declined || $this === self::Fail || $this === self::Error;
    }
}
