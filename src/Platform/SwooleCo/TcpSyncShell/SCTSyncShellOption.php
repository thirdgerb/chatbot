<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\SwooleCo\TcpSyncShell;

use Commune\Platform\SwooleCo\Supports\CoProcPoolOption;
use Commune\Platform\SwooleCo\Supports\CoTcpAdapterOption;
use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read CoProcPoolOption $poolOption
 * @property-read CoTcpAdapterOption $adapterOption
 */
class SCTSyncShellOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'poolOption' => [],
            'adapterOption' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'poolOption' => CoProcPoolOption::class,
            'adapterOption' => CoTcpAdapterOption::class,
        ];
    }


}