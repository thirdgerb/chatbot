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

    /**
     * @var boolean
     */
    protected $ran = false;

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
        if ($this->ran) {
            return null;
        }
        // 一个hearing 只运行一次
        $this->ran = true;

        /**
         * @var SimpleChatComponent $config
         */
        $config = $dialog->app->make(SimpleChatComponent::class);

        $id = $this->id ?? $config->default;

        // 检查是否匹配到了意图.
        $session = $dialog->session;
        $intentMessage = $session->getMatchedIntent()
            ?? $session
                ->intentRepo
                ->matchHighlyPossibleIntent($session);

        $intent = isset($intentMessage) ? $intentMessage->getName() : null;

        // 广义匹配
        if (empty($intent)) {
            return null;
        }

        $reply = Manager::match($id, $intent);

        if (isset($reply)) {
            static::reply($self, $dialog, $reply);
            return $this->navigate($dialog);
        }

        return null;
    }

    protected function navigate(Dialog $dialog) : Navigator
    {
        return $dialog->wait();
    }

    public static function reply(Context $self, Dialog $dialog, string $reply)
    {
        $dialog->say()
            ->withContext($self)
            ->info(trim($reply));
    }


}