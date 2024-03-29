<?php

namespace App\Lexer;

class GitVersionLexer extends BaseLexer
{
    public const T_NONE = 0;
    public const T_GIT_KEYWORD = 1;
    public const T_VERSION_KEYWORD = 2;
    public const T_VERSION_NUMBER = 3;
    public const T_VERSION_STRING = 4;

    public const REGEX_VERSION_STRING = '([0-9]+)\.([0-9]+)\.([0-9]+(?:\-[a-z]+)?)';
    public const REGEX_VERSION_NUMBER = '[0-9]+(?:\-[a-z]+)?';

    /**
     * Lexical catchable patterns.
     */
    protected function getCatchablePatterns(): array
    {
        return [
            'git',
            'version',
            self::REGEX_VERSION_STRING,
        ];
    }

    /**
     * Retrieve token type. Also processes the token value if necessary.
     *
     * @param string $value
     */
    protected function getType(&$value): int
    {
        if (is_string($value)) {
            switch (true) {
                case 'git' == $value:
                    return self::T_GIT_KEYWORD;
                case 'version' == $value:
                    return self::T_VERSION_KEYWORD;
                case preg_match('/^'.self::REGEX_VERSION_STRING.'$/', $value, $matches):
                    $value = $matches;

                    return self::T_VERSION_STRING;
                case preg_match('/^'.self::REGEX_VERSION_NUMBER.'$/', $value, $matches):
                    $value = $matches[0];

                    return self::T_VERSION_NUMBER;
            }
        }

        return self::T_NONE;
    }
}
