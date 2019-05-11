<?php

namespace App\Application;

interface ApplicationInterface
{
    public function getName(): string;

    public function getVersion(): ?string;

    public function isInstalled(): bool;

    public function isSupported(): bool;
}
