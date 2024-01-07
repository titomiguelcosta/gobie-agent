<?php

namespace App\Util;

use DateTime as BaseDateTime;

class DateTime
{
    public function now(): string
    {
        return (new \DateTimeImmutable())->format(BaseDateTime::ATOM);
    }
}
