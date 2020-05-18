<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cmd;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Framework\Command\AbsHelpCmd;
use Commune\Framework\Command\ARequestCmd;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class GhostCmd extends ARequestCmd
{
    use TGhostCmd;

    /**
     * 命令的规则定义. 使用了 Laravel 的命令 parser
     *
     * 具体的定义方法 @see https://laravel.com/docs/6.x/artisan#defining-input-expectations
     */
    const SIGNATURE = 'test';

    /**
     * 命令的简介, 用于介绍命令的功能.
     */
    const DESCRIPTION = '';

    protected function getHelpCmd(): AbsHelpCmd
    {
        return $this->getContainer()->make(GhostHelpCmd::class);
    }


}