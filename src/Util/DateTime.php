<?php

namespace App\Util;

use DateTime as BaseDateTime;
use DateTimeImmutable;

class DateTime
{
    public function now(): string
    {
        return (new DateTimeImmutable())->format(BaseDateTime::ATOM);
    }
}
