<?php


namespace Commune\Chatbot\App\Callables\Predictions;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Callables\Prediction;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;

/**
 * 用于 hearing::expect(), 作为条件.
 * 示范如何实现一个可以复用的 Prediction
 */
class IsContext implements Prediction
{
    /**
     * @var string
     */
    protected $name;

    /**
     * IsContext constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }


    public function __invoke(
        Context $self,
        Dialog $dialog,
        Message $message
    ): bool
    {
        return $message instanceof Context && $message->nameEquals($this->name);
    }


}