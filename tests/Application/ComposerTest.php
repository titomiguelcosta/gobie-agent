<?php

declare(strict_types=1);

namespace App\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use App\Application\Composer;
use Psr\Log\LoggerInterface;

class ComposerTest extends TestCase
{
    private $processor;
    private $logger;

    public function setUp(): void
    {
        $this->processor = $this->createMock(Process::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testDefaults()
    {
        $composer = new Composer($this->processor, $this->logger);
        $this->assertSame('Composer', $composer->getName());
        $this->assertFalse($composer->isInstalled());
        $this->assertFalse($composer->isSupported());
        $this->assertNull($composer->getVersion());
    }
}
