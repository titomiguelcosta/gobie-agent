<?php

namespace App\Util;

use DateTime as BaseDateTime;
use DateTimeImmutable;

class DateTime
{
    /**
     * @throws \Exception
     */
    public function now(): string
    {
        return (new DateTimeImmutable())->format(BaseDateTime::ISO8601);
    }
}
