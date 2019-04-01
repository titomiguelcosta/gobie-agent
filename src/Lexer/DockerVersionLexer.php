<?php

namespace App\Lexer;

use App\Model\Version;

class DockerVersionLexer extends BaseLexer
{
    const T_NONE = 0;
    const T_DOCKER_KEYWORD = 1;
    const T_VERSION_KEYWORD = 2;
    const T_VERSION_NUMBER = 3;
    const T_VERSION_IDENTIFIER = 4;
    const T_COMMA = 5;
    const T_BUILD_KEYWORD = 6;
    const T_BUILD_NUMBER = 7;

    const REGEX_VERSION = '[0-9]+\.[0-9]+\.[0-9]+(?:\-[a-z]+)?';
    const REGEX_BUILD = '[a-z0-9]+';

    /**
     * Lexical catchable patterns
     *
     * @return array
     */
    protected function getCatchablePatterns(): array
    {
        return [
            'Docker',
            'version',
            ',',
            'build',
            self::REGEX_VERSION,
            self::REGEX_BUILD,
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
                case 'Docker' == $value:
                    return self::T_DOCKER_KEYWORD;
                case 'version' == $value:
                    return self::T_VERSION_KEYWORD;
                case ',' == $value:
                    return self::T_COMMA;
                case 'build' == $value;

                    return self::T_BUILD_KEYWORD;
                case preg_match('/^'.self::REGEX_VERSION.'$/', $value):
                    // break down version into major, minor and patch
                    preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+(?:\-[a-z]+)?)$/', $value, $matches);
                    $value = new Version($matches[1], $matches[2], $matches[3]);

                    return self::T_VERSION_NUMBER;
                case preg_match('/^'.self::REGEX_BUILD.'$/', $value):
                        return self::T_BUILD_NUMBER;
            }
        }

        return self::T_NONE;
    }
}
