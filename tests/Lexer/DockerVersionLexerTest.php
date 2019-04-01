<?php

namespace App\Tests\Lexer;

use PHPUnit\Framework\TestCase;
use App\Lexer\DockerVersionLexer;
use App\Model\Version;

class DockerVersionLexerTest extends TestCase
{
    public function testValidDockerVersion()
    {
        $lexer = new DockerVersionLexer('Docker version 18.09.4, build d14af54266');
        $this->assertNull($lexer->lookahead);
        
        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_DOCKER_KEYWORD, $lexer->lookahead['type']);
        $this->assertSame('Docker', $lexer->lookahead['value']);
        $this->assertSame(0, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_VERSION_KEYWORD, $lexer->lookahead['type']);
        $this->assertSame('version', $lexer->lookahead['value']);
        $this->assertSame(7, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_VERSION_NUMBER, $lexer->lookahead['type']);
        $this->assertInstanceOf(Version::class, $lexer->lookahead['value']);
        $this->assertSame('18.09.4', $lexer->lookahead['value']->getVersion());
        $this->assertSame(15, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_COMMA, $lexer->lookahead['type']);
        $this->assertSame(',', $lexer->lookahead['value']);
        $this->assertSame(22, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_BUILD_KEYWORD, $lexer->lookahead['type']);
        $this->assertSame('build', $lexer->lookahead['value']);
        $this->assertSame(24, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_BUILD_NUMBER, $lexer->lookahead['type']);
        $this->assertSame('d14af54266', $lexer->lookahead['value']);
        $this->assertSame(30, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertNull($lexer->lookahead);
    }

    public function testDockerBuildStartingWithDigit()
    {
        $lexer = new DockerVersionLexer('Docker version 18.09.4, build 14af54266d');
        $this->assertNull($lexer->lookahead);
        
        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_DOCKER_KEYWORD, $lexer->lookahead['type']);
        $this->assertSame('Docker', $lexer->lookahead['value']);
        $this->assertSame(0, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_VERSION_KEYWORD, $lexer->lookahead['type']);
        $this->assertSame('version', $lexer->lookahead['value']);
        $this->assertSame(7, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_VERSION_NUMBER, $lexer->lookahead['type']);
        $this->assertInstanceOf(Version::class, $lexer->lookahead['value']);
        $this->assertSame('18.09.4', $lexer->lookahead['value']->getVersion());
        $this->assertSame(15, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_COMMA, $lexer->lookahead['type']);
        $this->assertSame(',', $lexer->lookahead['value']);
        $this->assertSame(22, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_BUILD_KEYWORD, $lexer->lookahead['type']);
        $this->assertSame('build', $lexer->lookahead['value']);
        $this->assertSame(24, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_BUILD_NUMBER, $lexer->lookahead['type']);
        $this->assertSame('14af54266d', $lexer->lookahead['value']);
        $this->assertSame(30, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertNull($lexer->lookahead);
    }

    public function testDockerVersionWithAlphanumericPatch()
    {
        $lexer = new DockerVersionLexer('Docker version 18.09.4-ce, build 14af54266d');
        $this->assertNull($lexer->lookahead);
        
        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_DOCKER_KEYWORD, $lexer->lookahead['type']);
        $this->assertSame('Docker', $lexer->lookahead['value']);
        $this->assertSame(0, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_VERSION_KEYWORD, $lexer->lookahead['type']);
        $this->assertSame('version', $lexer->lookahead['value']);
        $this->assertSame(7, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_VERSION_NUMBER, $lexer->lookahead['type']);
        $this->assertInstanceOf(Version::class, $lexer->lookahead['value']);
        $this->assertSame('18.09.4-ce', $lexer->lookahead['value']->getVersion());
        $this->assertSame(15, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_COMMA, $lexer->lookahead['type']);
        $this->assertSame(',', $lexer->lookahead['value']);
        $this->assertSame(25, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_BUILD_KEYWORD, $lexer->lookahead['type']);
        $this->assertSame('build', $lexer->lookahead['value']);
        $this->assertSame(27, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertIsArray($lexer->lookahead);
        $this->assertSame(DockerVersionLexer::T_BUILD_NUMBER, $lexer->lookahead['type']);
        $this->assertSame('14af54266d', $lexer->lookahead['value']);
        $this->assertSame(33, $lexer->lookahead['position']);

        $lexer->moveNext();
        $this->assertNull($lexer->lookahead);
    }
}