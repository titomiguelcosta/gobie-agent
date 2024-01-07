<?php

namespace App\Parser;

use App\Lexer\ComposerVersionLexer;

final class ComposerVersionParser
{
    private $lexer;

    /**
     * @var string
     */
    private $version = null;

    public function __construct(ComposerVersionLexer $composerVersionLexer)
    {
        $this->lexer = $composerVersionLexer;
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
            case ComposerVersionLexer::T_COMPOSER_KEYWORD:
                $this->parseComposerVersion();
                break;
            default:
                throw new \LogicException('Failed to parse initial token');
                break;
        }

        $this->lexer->moveNext();

        // Check for end of string
        if (null !== $this->lexer->lookahead) {
            throw new \LogicException('Failed to parse Composer');
        }
    }

    private function parseComposerVersion(): void
    {
        $succeeded = false;
        $this->lexer->moveNext();

        if (ComposerVersionLexer::T_VERSION_KEYWORD === $this->lexer->lookahead['type']) {
            $this->lexer->moveNext();

            if (ComposerVersionLexer::T_VERSION_STRING === $this->lexer->lookahead['type']) {
                $this->version = $this->lexer->lookahead['value'][0];
                $this->lexer->moveNext();
                $this->lexer->skipWhile(ComposerVersionLexer::T_VERSION_NUMBER);

                if (ComposerVersionLexer::T_DATE_STRING === $this->lexer->lookahead['type']) {
                    $succeeded = true;
                }
            }
        }

        if (!$succeeded) {
            throw new \LogicException('Failed to parse Composer version');
        }
    }
}
