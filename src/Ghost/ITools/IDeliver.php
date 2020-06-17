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
    /**
     * @var InputMsg
     */
    protected $input;

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var array
     */
    protected $slots = [];

    /**
     * @var int
     */
    protected $deliverAt = 0;


    /**
     * ITyper constructor.
     * @param Dialog $dialog
     */
    public function __construct(Dialog $dialog)
    {
        $this->dialog = $dialog;
        $this->input = $this->dialog->cloner->input;
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
        $cloner = $this->dialog->cloner;
        $output = $this->input->output(
            $message,
            $this->deliverAt
        );
        $cloner->output($output);
        return $this;
    }

    public function over(): Dialog
    {
        return $this->dialog;
    }


    public function __destruct()
    {
        $this->dialog = null;
        $this->input = null;
        $this->slots = [];
    }

}