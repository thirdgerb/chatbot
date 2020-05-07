<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Dialog\Finale;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Await
{

    /**
     * 重新开始进程.
     * @return Await
     */
    public function restartProcess() : Await;

    /**
     * @param string $query
     * @param array $suggestions
     * @param $defaultChoice
     * @param bool $withRoutes
     * @return Await
     */
    public function askChoose(
        string $query,
        array $suggestions,
        $defaultChoice = null,
        bool $withRoutes = true
    ) : Await;

    /**
     * 要求客户端确认.
     *
     * @param string $query
     * @param bool $default
     * @return Await
     */
    public function askConfirm(string $query, bool $default = true) : Await;

    /**
     * 要求客户端回答跟 Entity 相关的值.
     * @param string $query
     * @param string $entityName
     * @return Await
     */
    public function askEntity(
        string $query,
        string $entityName
    ) : Await;

    /**
     * 要客户端回答任何问题.
     *
     * @param string $query
     * @param array $suggestions
     * @return Await
     */
    public function askAny(
        string $query,
        array $suggestions = []
    ) : Await;

    /**
     * 要求客户端输入指定类型的消息.
     * @param string $protocal
     * @return Await
     */
    public function askMessage(string $protocal) : Await;

    /**
     * 要求客户端继续循环.
     * @param string $query
     * @param int $maxTurn
     * @return Await
     */
    public function askLoop(
        string $query,
        int $maxTurn
    ) : Await;
}