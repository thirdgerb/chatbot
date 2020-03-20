<?php


namespace Commune\Chatbot\OOHost\Context\Exiting;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class ExitingCatcher implements Exiting
{


    /**
     * @var Dialog
     */
    public $dialog;

    /**
     * @var Context
     */
    public $self;

    /**
     * @var int
     */
    protected $onExp;


    /**
     * @var Navigator|null
     */
    public $navigator;

    /**
     * @var Context|null
     */
    public $callback;

    /**
     * ExitingCatcher constructor.
     * @param int $onExp
     * @param Context $self
     * @param Dialog $dialog
     * @param Context|null $callback
     */
    public function __construct(
        int $onExp,
        Context $self,
        Dialog $dialog,
        Context $callback = null
    )
    {
        $this->dialog = $dialog;
        $this->self = $self;
        $this->onExp = $onExp;
        $this->callback = $callback;
    }

    public function onFulfill(callable $interceptor): Exiting
    {
        if ($this->onExp !== Definition::FULFILL || isset($this->navigator)) {
            return $this;
        }

        return $this->call($interceptor);
    }

    public function onQuit(callable $interceptor): Exiting
    {
        if ($this->onExp !== Definition::QUIT || isset($this->navigator)) {
            return $this;
        }

        return $this->call($interceptor);
    }


    public function onReject(callable $interceptor): Exiting
    {
        if ($this->onExp !== Definition::REJECTION || isset($this->navigator)) {
            return $this;
        }

        return $this->call($interceptor);
    }

    public function onCancel(callable $interceptor): Exiting
    {
        if ($this->onExp !== Definition::CANCEL || isset($this->navigator)) {
            return $this;
        }

        return $this->call($interceptor);
    }

    public function onFail(callable $interceptor): Exiting
    {
        if ($this->onExp !== Definition::FAILURE || isset($this->navigator)) {
            return $this;
        }

        return $this->call($interceptor);
    }

    public function onBackward(callable $interceptor): Exiting
    {
        if ($this->onExp !== Definition::BACKWARD || isset($this->navigator)) {
            return $this;
        }

        return $this->call($interceptor);
    }

    protected function call(callable $interceptor) : Exiting
    {
        $context = $this->self;
        $parameters = [
            'self' => $context,

        ];
        if (isset($this->callback)) {
            $parameters = [
                'callback' => $this->callback,
                get_class($this->callback) => $this->callback,
                Context::class => $this->callback,
            ] + $parameters;
        }

        $this->navigator = $this->dialog->app->call(
            $interceptor,
            $parameters,
            __METHOD__
        );
        return $this;
    }

}