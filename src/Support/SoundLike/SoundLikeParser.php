<?php

/**
 * Class SoundLikeParser
 * @package Commune\Support\SoundLike
 */

namespace Commune\Support\SoundLike;


interface SoundLikeParser
{

    public function soundLike(
        string $input,
        string $expect,
        int $compareType = SoundLikeInterface::COMPARE_EXACTLY
    ) : bool;


}