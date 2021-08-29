<?php

declare(strict_types=1);

namespace App\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use App\Application\Git;
use Psr\Log\LoggerInterface;

class GitTest extends TestCase
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
        $composer = new Git($this->processor, $this->logger);
        $this->assertSame('Git', $composer->getName());
        $this->assertFalse($composer->isInstalled());
        $this->assertFalse($composer->isSupported());
        $this->assertNull($composer->getVersion());
    }
}
