<?php

/**
 * Class SoundLikeManager
 * @package Commune\Support\SoundLike
 */

namespace Commune\Support\SoundLike;


use Commune\Support\Utils\StringUtils;

class SoundLikeManager implements SoundLikeInterface
{
    /**
     * @var SoundLikeParser[]
     */
    protected $parsers = [];

    public function registerParser(string $lang, SoundLikeParser $parser): void
    {
        $this->parsers[$lang] = $parser;
    }

    public function soundLike(
        string $input,
        string $expect,
        int $compareType = self::COMPARE_EXACTLY,
        string $lang = self::ZH
    ): bool
    {
        if (empty($input) || empty($expect)) {
            return false;
        }

        $input = StringUtils::normalizeString($input);
        $expect = StringUtils::normalizeString($expect);
        $parser = $this->parsers[$lang] ?? null;
        if (!isset($parser)) {
            return false;
        }
        return $parser->soundLike($input, $expect, $compareType);
    }


}