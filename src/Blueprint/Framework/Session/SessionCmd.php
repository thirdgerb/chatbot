<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Session;

use Commune\Blueprint\Framework\Command\CommandDef;
use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Framework\Session;
use Commune\Protocols\HostMsg;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionCmd extends LoggerInterface
{

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

    public function handleSession(
        Session $session,
        SessionCmdPipe $pipe,
        AppRequest $request,
        string $cmdText
    ) : Session;

    /**
     * @param Session $session
     * @return static
     */
    public function withSession(Session $session);

    public function sendError(array $errorBag) : void;

    public function output(HostMsg $message) : void;

    public function getDescription() : string;

    public function getCommandName() : string;

    public function getCommandDef() : CommandDef;
}