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

/**
 * Fallback Strategy 自身完全可以是一个 CodeContext.
 * 它可以从一个场景中创建出来, 也可以根据一个场景去应对.
 *
 * 作为策略, 它不应该把任何输入当成必要条件. 而应该去响应一个新的现场.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface FallbackStrategy
{
    const FUNC_CREATION = 'onCreation';
    const FUNC_HANDLER = 'onHandler';

    /**
     * 创建一个策略.
     * @param string $id
     * @return Ucl
     */
    public static function onCreation(string $id) : Ucl;

    /**
     * 执行一个策略.
     * @param string $id
     * @return Ucl
     */
    public static function onHandler(string $id) : Ucl;
}