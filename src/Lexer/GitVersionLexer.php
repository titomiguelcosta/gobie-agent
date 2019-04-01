<?php

namespace App\Lexer;

class GitVersionLexer extends BaseLexer
{
    const T_NONE = 0;
    const T_GIT_KEYWORD = 1;
    const T_VERSION_KEYWORD = 2;
    const T_VERSION_NUMBER = 3;
    const T_VERSION_STRING = 4;
    
    const REGEX_VERSION_STRING = '([0-9]+)\.([0-9]+)\.([0-9]+(?:\-[a-z]+)?)';
    const REGEX_VERSION_NUMBER = '[0-9]+(?:\-[a-z]+)?';

    /**
     * Lexical catchable patterns
     *
     * @return array
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
     * Retrieve token type. Also processes the token value if necessary
     *
     * @param string $value
     * @return integer
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
