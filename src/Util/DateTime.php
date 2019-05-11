<?php

namespace App\Util;

use DateTimeImmutable;
use DateTime as BaseDateTime;

class DateTime
{
    /**
     * @return string
     *
     * @throws \Exception
     */
    public function now(): string
    {
        return (new DateTimeImmutable())->format(BaseDateTime::ISO8601);
    }
}
