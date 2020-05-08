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
     * @var null|int
     */
    protected $deliverAt = null;

    /**
     * @var array
     */
    protected $outputs = [];

    /**
     * @var bool
     */
    protected $fin = false;

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

    public function error($intent, array $slots = array()): Typer
    {
        $this->outputs[] = [__FUNCTION__, $intent, $slots];
        return $this;
    }

    public function notice($intent, array $slots = array()): Typer
    {
        $this->outputs[] = [__FUNCTION__, $intent, $slots];
        return $this;
    }

    public function info($intent, array $slots = array()): Typer
    {
        $this->outputs[] = [__FUNCTION__, $intent, $slots];
        return $this;
    }

    public function debug($intent, array $slots = array()): Typer
    {
        $this->outputs[] = [__FUNCTION__, $intent, $slots];
        return $this;
    }

    public function fin(): Dialog
    {
        if ($this->fin) {
            return $this->dialog;
        }

        if (empty($this->outputs)) {
            $this->fin = true;
            return $this->dialog;
        }

        $input = $this->input;

        $outputs = array_map(function(array $output) use ($input){
            list($level, $reactionId, $slots) = $output;

            //todo
        }, $this->outputs);

        $cloner = $this->dialog->cloner;
        $cloner->output(...$outputs);
        $this->fin = true;
        return $this->dialog;
    }

    public function __destruct()
    {
        $this->fin();
        $this->outputs = [];
        $this->dialog = null;
    }

}