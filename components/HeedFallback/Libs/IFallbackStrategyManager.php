<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Libs;

use Commune\Components\HeedFallback\Data\FallbackStrategyInfo;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IFallbackStrategyManager implements FallbackStrategyManager
{

    protected $all = [];

    public function listStrategies(): array
    {
        return $this->all;
    }

    public function register(FallbackStrategyInfo $info): void
    {
        $this->all[$info->strategyClass] = $info;
    }


}