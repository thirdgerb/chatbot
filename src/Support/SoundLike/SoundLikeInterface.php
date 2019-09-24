<?php

/**
 * Class SoundLike
 * @package Commune\Support\SoundLike
 */

namespace Commune\Support\SoundLike;


interface SoundLikeInterface
{
    const ZH = 'zh';

    const COMPARE_EXACTLY = 0;
    const COMPARE_START_WITH = 1;
    const COMPARE_END_WITH = 2;
    const COMPARE_ANY_PART = 3;

    public function registerParser(string $lang, SoundLikeParser $parser) : void;

    public function soundLike(
        string $input,
        string $expect,
        int $compareType = self::COMPARE_EXACTLY,
        string $lang = self::ZH
    ) : bool;

}