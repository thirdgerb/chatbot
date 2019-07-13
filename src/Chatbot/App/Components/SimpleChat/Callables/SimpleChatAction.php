<?php


namespace Commune\Chatbot\App\Components\SimpleChat\Callables;

use Commune\Chatbot\App\Components\SimpleChat\Manager;
use Commune\Chatbot\App\Components\SimpleChatComponent;
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
     * @var string|null
     */
    protected $id;

    public function __construct(string $id = null)
    {
        $this->id = $id;
    }


    public function __invoke(
        Context $self,
        Dialog $dialog,
        Message $message
    ): ? Navigator
    {

        /**
         * @var SimpleChatComponent $config
         */
        $config = $dialog->app->make(SimpleChatComponent::class);

        $id = $this->id ?? $config->default;

        // 检查是否匹配到了意图.
        $intentMessage = $dialog->session->getMatchedIntent();
        $intent = isset($intentMessage) ? $intentMessage->getName() : null;

        // 广义匹配
        $intent = $intent ?? $dialog->session
            ->incomingMessage
            ->getMostPossibleIntent();

        if (empty($intent)) {
            return null;
        }

        $reply = Manager::match($id, $intent);

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