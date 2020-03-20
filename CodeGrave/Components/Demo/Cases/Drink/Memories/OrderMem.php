<?php


namespace Commune\Components\Demo\Cases\Drink\Memories;

use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;


/**
 * @property string $lastFruit 最后一次点的水果.
 * @property string $lastPack 最后一次的包装
 * @property string $lastIce 最后一次是否加冰
 *
 * @property int $times 来的次数
 */
class OrderMem extends MemoryDef
{
    const DESCRIPTION = '购买的记忆';

    const SCOPE_TYPES = [Scope::USER_ID];

    protected function init(): array
    {
        return [
            'times' => 0,
            'lastFruit' => null,
            'lastPack' => null,
            'lastIce' => null,
        ];
    }
}
