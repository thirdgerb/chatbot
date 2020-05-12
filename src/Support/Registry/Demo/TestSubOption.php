<?php


namespace Commune\Support\Registry\Demo;


use Commune\Support\Option\AbsOption;

class TestSubOption extends AbsOption
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => 1,
            'c' => 'C',
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}