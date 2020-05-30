<?php


namespace Commune\Support\Registry\Demo;

use Commune\Support\Option\AbsOption;

/**
 * @property-read string $id
 * @property-read string $a
 * @property-read string $b
 * @property-read TestSubOption[] $c
 */
class TestOption extends AbsOption
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => '1',
            'a' => 'A',
            'b' => 'B',
            'c' => [
                TestSubOption::stub(),
            ],
        ];
    }

    public static function relations(): array
    {
        return [
            'c[]' => TestSubOption::class
        ];
    }

}