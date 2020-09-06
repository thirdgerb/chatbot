<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\GhostCmd\Super;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Kernel\GhostCmd\AGhostCmd;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SceneCmd extends AGhostCmd
{
    const SIGNATURE = 'scene
       {--r|routes : 当前会话的路由状态}
       {--w|awaits : 监听中的用户意图}
       {--m|memory : 内存使用状态}
    ';

    const DESCRIPTION = '用于查看当前会话的环境变量';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $def = $this->getCommandDef();
        $options = $def->getOptions();
        $matched = 0;
        foreach ($options as $option) {
            $name = $option->getName();
            $exists = $message["--$name"] ?? false;

            if ($exists) {
                $method = "__$name";
                if (method_exists($this, $method)) {
                    $message = $this->{$method}();
                    $this->info($message);
                    $matched ++;
                }
            }
        }

        if ($matched === 0) {
            $this->notice("没有任何有效的参数. 请输入 -h 查看有效参数");
        }
    }

    protected function __routes() : string
    {
        $routes = $this->cloner->storage->shellSessionRoutes;
        return "当前会话路由状态: " . json_encode(
            $routes,
            ArrayAndJsonAble::PRETTY_JSON
        );
    }

    protected function __awaits() : string
    {
        $awaits = $this->cloner->runtime->getCurrentAwaitRoutes();
        $awaits = array_map([Ucl::class, 'encodeUcl'], $awaits);

        return "当前监听意图: " . json_encode(
            $awaits,
            ArrayAndJsonAble::PRETTY_JSON
        );
    }

    protected function __memory() : string
    {
        $peak = memory_get_peak_usage();

        $units = "kmg";
        $len = strlen($units);
        $t = '';
        for ($i = 0; $i < $len ; $i ++ ) {
            $t = $units[$i];
            $peak = $peak / 1024;
            if ($peak < 1024) {
                break;
            }
        }

        $peak = round($peak, 3);
        return "内存使用: $peak $t";
    }

}