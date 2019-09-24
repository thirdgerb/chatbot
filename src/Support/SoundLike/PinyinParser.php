<?php

/**
 * Class PinyinParser
 * @package Commune\Support\SoundLike
 */

namespace Commune\Support\SoundLike;


use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;

class PinyinParser implements SoundLikeParser
{
    /**
     * @var Pinyin
     */
    protected $pinyin;

    /**
     * PinyinParser constructor.
     * @param Pinyin $pinyin
     */
    public function __construct(Pinyin $pinyin)
    {
        $this->pinyin = $pinyin;
    }

    public function soundLike(
        string $input,
        string $expect,
        int $compareType = SoundLikeInterface::COMPARE_EXACTLY
    ): bool
    {
        $option = PINYIN_NO_TONE |PINYIN_KEEP_NUMBER | PINYIN_KEEP_ENGLISH;
        $inputArr = $this->pinyin->convert($input, $option);
        $expectArr = $this->pinyin->convert($expect, $option);

        $inputStr = implode(',', $inputArr);
        $expectStr = implode(',', $expectArr);

        switch ($compareType) {
            case SoundLikeInterface::COMPARE_END_WITH :
                return Str::endsWith($expectStr, $inputStr);
            case SoundLikeInterface::COMPARE_START_WITH :
                return Str::startsWith($expectStr, $inputStr);
            case SoundLikeInterface::COMPARE_ANY_PART :
                return Str::contains($expectStr, $inputStr);
            default :
                return $inputStr === $expectStr;
        }
    }


}