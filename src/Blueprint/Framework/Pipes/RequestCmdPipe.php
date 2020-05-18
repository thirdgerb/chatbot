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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RequestCmdPipe extends RequestPipe
{

    /**
     * 检查命令名是否存在.
     * @param string $commandName
     * @return bool
     */
    public function hasCommand(string $commandName) : bool;

    /**
     * 命令的类名, 在 ioc 容器里用它来生成命令对象.
     * @param string $commandName
     * @return string
     */
    public function getCommandId(string $commandName) : string;

    /**
     * 当前管道里命令的标识符. 例如 '/', '#'
     * @return string
     */
    public function getCommandMark() : string;

    /**
     * 所有命令的简介
     * @return string[]
     */
    public function getDescriptions() : array;

    /**
     * 单个命令的简介
     * @param string $commandName
     * @return string
     */
    public function getCommandDesc(string $commandName) : string;

}