<?php


namespace Commune\Chatbot\App\Callables\Intercepers;


use Commune\Chatbot\OOHost\Context\Callables\Interceptor;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 用于赋值的中间件, 也是示范如何减少闭包代码.
 */
class ContextSetter implements Interceptor
{
    protected $setters = [];

    public function with(string $key, $value) : ContextSetter
    {
        $this->setters[$key] = $value;
        return $this;
    }

    public function __invoke(
        Context $self,
        Dialog $dialog
    ): ? Navigator
    {
        foreach ($this->setters as $key => $value) {
            $self->__set($key, $value);
        }
        return null;
    }


}