<?php

namespace App\Lexer;

use Doctrine\Common\Lexer\AbstractLexer;

abstract class BaseLexer extends AbstractLexer
{
    /**
     * @param string $input version output
     */
    public function __construct($input)
    {
        $this->setInput($input);
    }

    /**
     * Tells the lexer to skip input tokens while it sees a token with the given value.
     *
     * @param string $type the token type to skip while
     */
    public function skipWhile($type)
    {
        while (null !== $this->lookahead && $this->lookahead['type'] === $type) {
            $this->moveNext();
        }
    }

    /**
     * Lexical non-catchable patterns.
     */
    protected function getNonCatchablePatterns(): array
    {
        return [
            '\s+',
            '(.)',
        ];
    }
}
