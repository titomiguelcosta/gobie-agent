<?php

namespace App\Lexer;

class ComposerVersionLexer extends BaseLexer
{
    public const T_NONE = 0;
    public const T_COMPOSER_KEYWORD = 1;
    public const T_VERSION_KEYWORD = 2;
    public const T_VERSION_NUMBER = 3;
    public const T_VERSION_STRING = 4;
    public const T_DATE_STRING = 5;

    public const REGEX_VERSION_STRING = '([0-9]+)\.([0-9]+)\.([0-9]+(?:\-[a-z]+)?)';
    public const REGEX_VERSION_NUMBER = '[0-9]+(?:\-[a-z]+)?';
    public const REGEX_DATE = '[0-9]{4}\-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}';

    /**
     * Lexical catchable patterns.
     */
    protected function getCatchablePatterns(): array
    {
        return [
            'Composer',
            'version',
            self::REGEX_VERSION_STRING,
            self::REGEX_DATE,
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
                case 'Composer' == $value:
                    return self::T_COMPOSER_KEYWORD;
                case 'version' == $value:
                    return self::T_VERSION_KEYWORD;
                case preg_match('/^'.self::REGEX_VERSION_STRING.'$/', $value, $matches):
                    $value = $matches;

                    return self::T_VERSION_STRING;
                case preg_match('/^'.self::REGEX_VERSION_NUMBER.'$/', $value, $matches):
                    return self::T_VERSION_NUMBER;
                case preg_match('/^'.self::REGEX_DATE.'$/', $value, $matches):
                    $value = $matches[0];

                    return self::T_DATE_STRING;
            }
        }

        return self::T_NONE;
    }
}
