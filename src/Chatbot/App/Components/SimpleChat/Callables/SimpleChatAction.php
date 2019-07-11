<?php


namespace Commune\Chatbot\App\Components\SimpleChat\Callables;

use Commune\Chatbot\App\Components\SimpleChat\Manager;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Callables\Action;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 用在hearing 内作为一个 action, 最好是 end() 方法或者 interceptor() 方法处使用.
 */
class SimpleChatAction implements Action
{
    /**
     * @var int|string
     */
    protected $resourceIndex;

    /**
     * SimpleChatAction constructor.
     * @param int|string $resourceIndex
     */
    public function __construct($resourceIndex)
    {
        $this->resourceIndex = $resourceIndex;
    }


    public function __invoke(
        Context $self,
        Dialog $dialog,
        Message $message
    ): ? Navigator
    {
        $intent = $dialog->session
            ->incomingMessage
            ->getMostPossibleIntent();

        if (empty($intent)) {
            return null;
        }

        $reply = Manager::match($this->resourceIndex, $intent);

        if (isset($reply)) {
            static::reply($self, $dialog, $reply);
            return $dialog->wait();
        }

        return null;
    }

    public static function reply(Context $self, Dialog $dialog, string $reply)
    {
        $dialog->say()
            ->withContext($self)
            ->info($reply);
    }


}