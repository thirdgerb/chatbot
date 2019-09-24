<?php

/**
 * Class SubDialogBuilderImpl
 * @package Commune\Chatbot\OOHost\Context\Stages
 */

namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\SubDialog;
use Commune\Chatbot\OOHost\Directing\End\MissMatch;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Directing\Stage\CallbackStage;

class SubDialogBuilderImpl implements SubDialogBuilder, RunningSpy
{
    use RunningSpyTrait;

    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var string
     */
    protected $belongsTo;

    /**
     * @var callable
     */
    protected $rootContextMaker;

    /**
     * @var bool
     */
    protected $keepAlive;

    /**
     * @var SubDialog
     */
    protected $subDialog;

    /**
     * @var Message|null
     */
    protected $message;

    /**
     * @var Navigator|null
     */
    protected $navigator;

    /**
     * SubDialogBuilderImpl constructor.
     * @param Stage $stage
     * @param string $belongsTo
     * @param callable $rootContextMaker
     * @param bool $keepAlive
     * @param Message|null $message
     */
    public function __construct(Stage $stage, string $belongsTo, callable $rootContextMaker, Message $message = null, bool $keepAlive = true )
    {
        $this->stage = $stage;
        $this->belongsTo = $belongsTo;
        $this->rootContextMaker = $rootContextMaker;
        $this->keepAlive = $keepAlive;
        $this->message = $message;

        // 强迫清空掉
        // 也只是进入的时候才清空.
        if ($stage->isStart() && !$this->keepAlive) {
            $this->stage
                ->dialog
                ->session
                ->repo
                ->clearSnapshot($this->belongsTo);
        }
        static::addRunningTrace($this->belongsTo, $this->belongsTo);
    }


    public function onBefore(callable $callable): SubDialogBuilder
    {
        if (isset($this->navigator)) return $this;

        if (!$this->stage->isCallback()) {
            return $this;
        }

        $dialog = $this->stage->dialog;
        $navigator = $dialog
            ->app
            ->callContextInterceptor(
                $dialog->currentContext(),
                $callable
            );

        if (!is_null($navigator) && !$navigator instanceof MissMatch) {
            $this->navigator = $navigator;
        }

        return $this;
    }

    public function onQuit(callable $callable): SubDialogBuilder
    {
        if (isset($this->navigator)) return $this;

        $this->getSubDialog()->onQuit($callable);
        return $this;
    }

    public function onMiss(callable $callable): SubDialogBuilder
    {
        if (isset($this->navigator)) return $this;
        $this->getSubDialog()->onMiss($callable);
        return $this;
    }

    public function onWait(callable $callable): SubDialogBuilder
    {
        if (isset($this->navigator)) return $this;
        $this->getSubDialog()->onWait($callable);
        return $this;
    }

    public function end(): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        $navigator = $this->stage->navigator;
        if (isset($navigator)) {
            return $navigator;
        }

        if ($this->stage->isStart()) {
            return $this->getSubDialog()->repeat();
        }

        if ($this->stage->isCallback()) {
            $subDialog = $this->getSubDialog();
            return new CallbackStage($subDialog, $subDialog->currentMessage());
        }

        return $this->stage->dialog->wait();
    }

    protected function getSubDialog() : SubDialog
    {
        if (isset($this->subDialog)) {
            return $this->subDialog;
        }
        return $this->subDialog = $this->stage->dialog->getSubDialog($this->belongsTo, $this->rootContextMaker, $this->message);
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->belongsTo);
    }
}