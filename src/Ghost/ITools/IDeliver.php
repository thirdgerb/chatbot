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

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Message\Host\IIntentMsg;
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
    protected $shellName = null;

    /**
     * @var null|string
     */
    protected $sessionId = null;

    /**
     * @var null|string
     */
    protected $guestId = null;


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
        $this->input = $this->dialog->cloner->input;
        $this->immediately = $immediately;
    }


    public function withSlots(array $slots): Deliver
    {
        $this->slots = $slots;
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
        $intentMsg = new IIntentMsg($intent, $slots, $level);
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

        $messages = array_map(function(HostMsg $message){
            return $this->input->output(
                $message,
                $this->deliverAt,
                $this->shellName,
                $this->sessionId,
                $this->guestId
            );
        }, $messages);

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
        $this->dialog = null;
        $this->input = null;
        $this->slots = [];
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