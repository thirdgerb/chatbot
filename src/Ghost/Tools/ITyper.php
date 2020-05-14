<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Tools;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Tools\Typer;
use Commune\Message\Host\IIntentMsg;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\GhostInput;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ITyper implements Typer
{
    /**
     * @var GhostInput
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
     * @var null|string
     */
    protected $guestId = null;

    /**
     * @var null|string
     */
    protected $clonerId = null;

    /**
     * @var null|string
     */
    protected $shellName = null;

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
        $this->input = $this->dialog->cloner->ghostInput;
    }


    public function withSlots(array $slots): Typer
    {
        $this->slots = $slots;
        return $this;
    }

    public function toGuest(string $guestId): Typer
    {
        $this->guestId = $guestId;
        return $this;
    }

    public function toClone(string $cloneId): Typer
    {
        $this->clonerId = $cloneId;
        return $this;
    }

    public function toShell(string $shellName): Typer
    {
        $this->shellName = $shellName;
        return $this;
    }

    /**
     * 指定发送的时间.
     * @param int $timestamp
     * @return Typer
     */
    public function deliverAt(int $timestamp) : Typer
    {
        $this->deliverAt = $timestamp;
        return $this;
    }

    public function deliverAfter(int $sections): Typer
    {
        $this->deliverAt = time() + $sections;
        return $this;
    }

    public function error(string $intent, array $slots = array()): Typer
    {
        return $this->log(__FUNCTION__, $intent, $slots);
    }

    public function notice(string $intent, array $slots = array()): Typer
    {
        return $this->log(__FUNCTION__, $intent, $slots);
    }

    public function info(string $intent, array $slots = array()): Typer
    {
        return $this->log(__FUNCTION__, $intent, $slots);
    }

    public function debug(string $intent, array $slots = array()): Typer
    {
        return $this->log(__FUNCTION__, $intent, $slots);
    }

    protected function log(string $level, string $intent, array $slots) : Typer
    {
        $intentMsg = new IIntentMsg($intent, $slots, $level);
        return $this->message($intentMsg);
    }

    public function message(HostMsg $message): Typer
    {
        $ghostMsg = $this->input->output(
            $message,
            $this->deliverAt,
            $this->clonerId,
            $this->shellName,
            $this->guestId
        );

        $this->dialog->cloner->output($ghostMsg);
        return $this;
    }

    public function __destruct()
    {
        $this->dialog = null;
        $this->input = null;
        $this->slots = [];
    }

}