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
use Commune\Kernel\GhostCmd\AGhostCmd;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class EnvCmd extends AGhostCmd
{
    const SIGNATURE = 'env
       {--route : 当前会话的路由状态}
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
            $this->notice("没有任何有效的参数. 请输入 env -h 查看有效参数");
        }
    }

    protected function __route() : string
    {
        $routes = $this->cloner->storage->shellSessionRoutes;
        return "当前会话路由状态: " . json_encode($routes);
    }



}