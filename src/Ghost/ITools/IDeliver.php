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
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\InputMsg;

/**
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
     * @var int
     */
    protected $deliverAt = 0;

    /**
     * @var array
     */
    protected $slots = [];

    /**
     * @var null|string
     */
    protected $shellId = null;

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

    public function withSessionId(string $sessionId) : Deliver
    {
        $this->sessionId = $sessionId;
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

    protected function output(HostMsg $message, HostMsg ...$messages) : void
    {
        // 有可能要做异步.
        array_unshift($messages, $message);

        // 准备所有参数
        $selfSessionId = $this->cloner->getSessionId();
        $sessionId = $this->sessionId ?? $selfSessionId;

        // is async ?
        $async = $this->sessionId === $selfSessionId;

        // 如果不是相同的 session, 则不设置 convoId
        $convoId = $async
            ? $this->cloner->getConversationId()
            : '';

        $app = $this->cloner->getApp();

        $appId = $this->creatorId ?? $app->getId();
        $appName = $this->creatorName ?? $app->getName();
        $deliverAt = $this->deliverAt;


        // 遍历赋值.
        $messages = array_map(
            function(HostMsg $message) use (
                $async,
                $deliverAt,
                $sessionId,
                $convoId,
                $appId,
                $appName
            ){
                if ($async) {
                    return new IInputMsg([

                    ]);
                }

                return $this->input->output(
                    $message,
                    $deliverAt,
                    $sessionId,
                    $convoId,
                    $appId,
                    $appName
                );
            },
            $messages
        );

        $cloner = $this->dialog->cloner;
        $cloner->output(...$messages);
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