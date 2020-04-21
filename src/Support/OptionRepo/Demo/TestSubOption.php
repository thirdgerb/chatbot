<?php


namespace Commune\Support\OptionRepo\Demo;


use Commune\Support\Option;

class TestSubOption extends Option
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => 1,
            'c' => 'C',
        ];
    }


}