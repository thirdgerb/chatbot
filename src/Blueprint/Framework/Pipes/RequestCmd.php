<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Pipes;

use Commune\Container\ContainerContract;
use Psr\Log\LoggerInterface;
use Commune\Protocals\HostMsg;
use Commune\Blueprint\Framework\Command\CommandDef;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RequestCmd extends LoggerInterface
{
    public function getContainer() : ContainerContract;

    public function handleSession(
        AppRequest $request,
        RequestCmdPipe $pipe,
        string $cmdText
    ) : ? AppResponse;

    public function sendError(array $errorBag) : void;

    /**
     * 继续往后走. 默认会中断.
     */
    public function goNext() : void;

    public function output(HostMsg $message) : void;

    public function getDescription() : string;

    public static function getCommandName() : string;

    public static function getCommandDef() : CommandDef;

}