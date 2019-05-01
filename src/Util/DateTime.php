<?php

namespace App\Util;

use DateTimeImmutable;
use DateTime as BaseDateTime;

class DateTime
{
    public function now(): string
    {
        return (new DateTimeImmutable())->format(BaseDateTime::ISO8601);
    }
}
