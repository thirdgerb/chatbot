<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ITools;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Message\Host\IIntentMsg;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\IntercomMsg;

/**
 * 投递消息的链式调用工具.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IDeliver implements Deliver
{

    /*----- property -----*/

    /**
     * @var InputMsg
     */
    protected $input;

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var bool
     */
    protected $immediately;

    /*----- output config -----*/

    /**
     * @var int|null
     */
    protected $deliverAt = null;

    /**
     * @var array
     */
    protected $slots = [];

    /**
     * @var null|string
     */
    protected $sessionId = null;

    /**
     * @var null|string
     */
    protected $creatorId = null;

    /**
     * @var null|string
     */
    protected $creatorName = null;

    /**
     * @var bool
     */
    protected $isDelivery = false;

    /*---- cached ----*/

    /**
     * @var HostMsg[]
     */
    protected $buffer = [];



    /**
     * IDeliver constructor.
     * @param Dialog $dialog
     * @param bool $immediately
     */
    public function __construct(Dialog $dialog, bool $immediately)
    {
        $this->dialog = $dialog;
        $this->cloner = $dialog->cloner;
        $this->input = $this->cloner->input;
        $this->immediately = $immediately;
    }


    public function withSlots(array $slots): Deliver
    {
        $this->slots = $slots;
        return $this;
    }

    /**
     * @param string $sessionId
     * @param bool $isDelivery
     * @return Deliver
     */
    public function withSessionId(string $sessionId, bool $isDelivery = false) : Deliver
    {
        $this->sessionId = $sessionId;
        $this->isDelivery = $isDelivery;
        return $this;
    }

    public function withCreator(string $creatorId, string $creatorName = '') : Deliver
    {
        $this->creatorId = $creatorId;
        $this->creatorName = $creatorName;
        return $this;
    }

    /**
     * 指定发送的时间.
     * @param int $timestamp
     * @return Deliver
     */
    public function deliverAt(int $timestamp) : Deliver
    {
        $this->deliverAt = $timestamp;
        return $this;
    }

    public function deliverAfter(int $sections): Deliver
    {
        $this->deliverAt = time() + $sections;
        return $this;
    }

    public function error(string $intent, array $slots = array()): Deliver
    {
        return $this->log(__FUNCTION__, $intent, $slots);
    }

    public function notice(string $intent, array $slots = array()): Deliver
    {
        return $this->log(__FUNCTION__, $intent, $slots);
    }

    public function info(string $intent, array $slots = array()): Deliver
    {
        return $this->log(__FUNCTION__, $intent, $slots);
    }

    public function debug(string $intent, array $slots = array()): Deliver
    {
        return $this->log(__FUNCTION__, $intent, $slots);
    }

    protected function log(string $level, string $intent, array $slots) : Deliver
    {
        $slots = $slots + $this->slots;
        $intentMsg = IIntentMsg::newIntent($intent, $slots, $level);
        return $this->message($intentMsg);
    }

    public function message(HostMsg $message): Deliver
    {
        if ($this->immediately) {
            $this->output($message);

        } else {
            $this->buffer[] = $message;
        }

        return $this;
    }

    /**
     * 准备输出的消息.
     * @param HostMsg $message
     * @param HostMsg ...$messages
     */
    protected function output(HostMsg $message, HostMsg ...$messages) : void
    {
        // 有可能要做异步.
        array_unshift($messages, $message);


        $cloner = $this->dialog->cloner;

        // 准备所有参数
        $selfSessionId = $this->cloner->getSessionId();

        // 检查是否指定了不同于当前 session 的 sessionId
        $sessionId = $this->sessionId ?? $selfSessionId;

        // is async ? 如果目标的 SessionId 不相同, 则是异步消息.
        $async = $this->isDelivery
            || ($sessionId !== $selfSessionId);

        $messages = $this->prepareOutputs($sessionId, $async, $messages);

        // 正常 buffer 回复消息
        if (! $async) {
            $cloner->output(...$messages);

        // buffer 异步投递的回复消息
        } elseif ($this->isDelivery ){
            $cloner->asyncDeliver(...$messages);

        // buffer 异步的输入消息.
        } else {
            $cloner->asyncInput(...$messages);
        }
    }

    /**
     * @param string $sessionId
     * @param bool $async
     * @param array $messages
     * @return IntercomMsg[]
     */
    protected function prepareOutputs(
        string $sessionId,
        bool $async,
        array $messages
    ) : array
    {

        // Id 为空是对话机器人消息的标志.
        // 由客户端去判断用什么身份来代表机器人.
        $creatorId = $this->creatorId ?? $this->cloner->avatar->getId();
        $creatorName = $this->creatorName ?? $this->cloner->avatar->getName();
        $deliverAt = $this->deliverAt;

        // 遍历赋值.
        $messages = array_map(
            function(HostMsg $message) use (
                $async, // 异步发送
                $deliverAt, // 投递时间
                $sessionId, // 投递的目标 Session
                $creatorId, // 投递的 creator 信息
                $creatorName
            ){
                // 异步时发送的都是 inputMsg
                // 因此用 divide 来克隆消息体.
                if ($async) {
                    $asyncInput = $this->input->divide(
                        $message,
                        $sessionId,
                        '',
                        $creatorId,
                        $creatorName,
                        $deliverAt
                    );
                    return $asyncInput;
                }

                // 否则发送同步消息.
                return $this->input->output(
                    $message,
                    $creatorId,
                    $creatorName,
                    $deliverAt
                );

            },
            $messages
        );

        return $messages;
    }

    public function over(): Dialog
    {
        $this->__invoke();
        return $this->dialog;
    }


    public function __destruct()
    {
        unset(
            $this->cloner,
            $this->input,
            $this->dialog,
            $this->buffer
        );
    }

    public function __invoke(): void
    {
        if (!empty($this->buffer)) {
            $buffer = $this->buffer;
            $this->output(...$buffer);
            $this->buffer = [];
        }
    }


}