<?php

namespace App\Lexer;

use Doctrine\Common\Lexer\AbstractLexer;

class DockerVersionLexer extends AbstractLexer
{
    const T_NONE = 0;
    const T_VERSION_NUMBER = 1;
    const T_BUILD_NUMBER = 2;
    const T_COMMA = 3;
    const T_DOCKER_KEYWORD = 4;
    const T_VERSION_KEYWORD = 5;
    const T_BUILD_KEYWORD = 6;

    const REGEX_VERSION = '([0-9]+)\.([0-9]+)\.([0-9]+(\-[a-z]+)?)';
    const REGEX_BUILD = '([a-z0-9]+)';

    /**
     * Creates a new query scanner object.
     *
     * @param string $input a query string
     */
    public function __construct($input)
    {
        $this->setInput($input);
    }

    /**
     * Lexical catchable patterns.
     *
     * @return array
     */
    protected function getCatchablePatterns(): array
    {
        return [
            'Docker',
            'version',
            self::REGEX_VERSION,
            ',',
            'build',
            self::REGEX_BUILD,
        ];
    }

    /**
     * Lexical non-catchable patterns.
     *
     * @return array
     */
    protected function getNonCatchablePatterns(): array
    {
        return [
            '\s+',
            '(.)',
        ];
    }

    /**
     * Retrieve token type. Also processes the token value if necessary.
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
                case preg_match('/^'.self::REGEX_VERSION.'$/', $value, $matches):
                    // get major, minor and patch versions assigned to the value
                    $value = $matches;

                    return self::T_VERSION_NUMBER;
                case preg_match('/^'.self::REGEX_BUILD.'$/', $value):
                    return self::T_BUILD_NUMBER;
            }
        }

        return self::T_NONE;
    }
}
