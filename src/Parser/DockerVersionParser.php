<?php

namespace App\Parser;

use App\Lexer\DockerVersionLexer;

class DockerVersionParser
{
    /**
     * @var DockerVersionLexer
     */
    private $lexer;

    /**
     * @var string
     */
    protected $version = null;

    /**
     * @var string
     */
    protected $build = null;

    public function __construct($dql)
    {
        $this->lexer = new DockerVersionLexer($dql);
        $this->parse();
    }

    /**
     * @return string
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getBuild(): ?string
    {
        return $this->build;
    }

    private function parse(): void
    {
        $this->lexer->moveNext();

        switch ($this->lexer->lookahead['type']) {
            case DockerVersionLexer::T_DOCKER_KEYWORD:
                $this->parseDockerVersion();
                break;
            default:
                throw new \LogicException('Failed to parse string');
                break;
        }

        $this->lexer->moveNext();

        // Check for end of string
        if (null !== $this->lexer->lookahead) {
            throw new \LogicException('Failed to parse docker');
        }
    }

    private function parseDockerVersion(): void
    {
        $succeeded = false;
        $this->lexer->moveNext();

        if (DockerVersionLexer::T_VERSION_KEYWORD === $this->lexer->lookahead['type']) {
            $this->lexer->moveNext();

            if (DockerVersionLexer::T_VERSION_NUMBER === $this->lexer->lookahead['type']) {
                $this->version = $this->lexer->lookahead['value'][0];
                // skip all the 3 tokens for the version                
                $this->lexer->moveNext();
                $this->lexer->moveNext();
                $this->lexer->moveNext();
                $this->lexer->moveNext();
                
                if ($this->lexer->isNextToken(DockerVersionLexer::T_COMMA)) {
                    $this->lexer->moveNext();
                }
                if ($this->lexer->isNextToken(DockerVersionLexer::T_BUILD_KEYWORD)) {
                    $this->lexer->moveNext();
                }

                if (DockerVersionLexer::T_BUILD_NUMBER === $this->lexer->lookahead['type']) {
                    $this->build = $this->lexer->lookahead['value'];
                    $this->lexer->moveNext();
                    $succeeded = true;
                }
            }
        }

        if (!$succeeded) {
            throw new \LogicException('Failed to parse docker version');
        }
    }
}