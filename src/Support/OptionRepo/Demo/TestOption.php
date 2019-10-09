<?php


namespace Commune\Support\OptionRepo\Demo;

use Commune\Support\Option;

/**
 * @property-read int $id
 * @property-read string $a
 * @property-read string $b
 * @property-read TestSubOption[] $c
 */
class TestOption extends Option
{
    const IDENTITY = 'id';

    protected static $associations = [
        'c[]' => TestSubOption::class
    ];

    public static function stub(): array
    {
        return [
            'id' => 123,
            'a' => 'A',
            'b' => 'B',
            'c' => []
        ];
    }


}