<?php


namespace Commune\Components\Story\Options;


use Commune\Support\Option;

/**
 * @property-read string $query
 * @property-read array $ifItem 条件.
 * @property-read ChoiceOption[] $choices
 */
class ChooseOption extends Option
{
    const IDENTITY = 'query';

    protected static $associations = [
        'choices[]' => ChoiceOption::class,
    ];

    public static function stub(): array
    {
        return [
            'query' => '',
            'ifItem' => [],
            'choices' => [],
        ];
    }


}