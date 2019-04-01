<?php

namespace App\Parser;

use App\Lexer\GitVersionLexer;

class GitVersionParser
{
    /**
     * @var GitVersionLexer
     */
    private $lexer;

    /**
     * @var string
     */
    protected $version = null;

    /**
     * @param GitVersionLexer $gitVersionLexer
     */
    public function __construct(GitVersionLexer $gitVersionLexer)
    {
        $this->lexer = $gitVersionLexer;
        $this->parse();
    }

    /**
     * @return string
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    private function parse(): void
    {
        $this->lexer->moveNext();

        switch ($this->lexer->lookahead['type']) {
            case GitVersionLexer::T_GIT_KEYWORD:
                $this->parseGitVersion();
                break;
            default:
                throw new \LogicException('Failed to parse initial token');
                break;
        }

        $this->lexer->moveNext();

        // Check for end of string
        if (null !== $this->lexer->lookahead) {
            throw new \LogicException('Failed to parse Git');
        }
    }

    private function parseGitVersion(): void
    {
        $succeeded = false;
        $this->lexer->moveNext();

        if (GitVersionLexer::T_VERSION_KEYWORD === $this->lexer->lookahead['type']) {
            $this->lexer->moveNext();

            if (GitVersionLexer::T_VERSION_STRING === $this->lexer->lookahead['type']) {
                $this->version = $this->lexer->lookahead['value'][0];
                $this->lexer->moveNext();
                // skip individual version numbers
                $this->lexer->skipWhile(GitVersionLexer::T_VERSION_NUMBER);
                $succeeded = true;
            }
        }

        if (!$succeeded) {
            throw new \LogicException('Failed to parse Git version');
        }
    }
}