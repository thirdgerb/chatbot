<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Commands\User;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Ghost\Cmd\AGhostCmd;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HelloCmd extends AGhostCmd
{
    /**
     * 命令的规则定义. 使用了 Laravel 的命令 parser
     *
     * 具体的定义方法 @see https://laravel.com/docs/6.x/artisan#defining-input-expectations
     */
    const SIGNATURE = 'hello';

    /**
     * 命令的简介, 用于介绍命令的功能.
     */
    const DESCRIPTION = '测试命令';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $this->info('hello world!');
    }


}