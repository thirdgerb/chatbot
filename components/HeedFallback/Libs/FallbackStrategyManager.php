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

use Commune\Blueprint\Ghost\Ucl;
use Commune\Components\HeedFallback\Data\FallbackStrategyInfo;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface FallbackStrategyManager
{

    /**
     * @return FallbackStrategyInfo[]
     */
    public function listStrategies() : array;

    public function register(FallbackStrategyInfo $info) : void;

    public function getCreation(string $strategyClass, string $id) : Ucl;

    public function getHandler(string $strategyClass, string $id) : Ucl;

}