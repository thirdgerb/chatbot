<?php


namespace Commune\Chatbot\App\Callables\Predictions;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Callables\Prediction;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;

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