<?php

namespace App\Tests\Parser;

use PHPUnit\Framework\TestCase;
use App\Parser\DockerVersionParser;
use App\Lexer\DockerVersionLexer;

class DockerVersionParserTest extends TestCase
{
    public function testValidDockerVersion()
    {
        $parser = new DockerVersionParser(new DockerVersionLexer('Docker version 18.09.4, build d14af54266'));

        $this->assertSame('18.09.4', $parser->getVersion());
        $this->assertSame('d14af54266', $parser->getBuild());
    }
}