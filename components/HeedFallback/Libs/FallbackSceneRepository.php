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

use Commune\Components\HeedFallback\Data\FallbackSceneOption;


/**
 * 任务的仓库.
 *
 * 需要有一个可以查询到任务的列表, 和一个线性排列任务的管道.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface FallbackSceneRepository
{
    public function count() : int;

    public function push(FallbackSceneOption $option, bool $toPipe = true) : bool;

    public function find(string $id) : ? FallbackSceneOption;

    public function pop() : ? FallbackSceneOption;

    public function delete(string $id) : bool;

    public function flush() : void;
}