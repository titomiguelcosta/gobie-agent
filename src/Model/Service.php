<?php

namespace App\Model;

class Service
{
    protected $image;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function getImage(): string
    {
        return $this->image;
    }
}
