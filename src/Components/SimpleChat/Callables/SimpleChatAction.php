<?php


namespace Commune\Components\SimpleChat\Callables;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Callables\Action;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\SimpleChat\Options\ChatOption;
use Commune\Support\OptionRepo\Contracts\OptionRepository;

/**
 * 用在hearing 内作为一个 action, 最好是 end() 方法或者 interceptor() 方法处使用.
 */
class SimpleChatAction implements Action
{
    /**
     * @var array
     */
    protected $slots = [];

    /**
     * @var callable|null
     */
    protected $then;

    public function withSlots(array $slots) : SimpleChatAction
    {
        $this->slots = $slots;
        return $this;
    }


    public function then(callable $then) : SimpleChatAction
    {
        $this->then = $then;
        return $this;
    }


    public function __invoke(
        Context $self,
        Dialog $dialog,
        Message $message
    ): ? Navigator
    {
        // 检查是否匹配到了意图.
        $session = $dialog->session;
        $intent = $session->getMatchedIntent();

        // 没匹配到就不关自己的事情了.
        if (empty($intent)) {
            return null;
        }

        $name = isset($intent) ? $intent->getName() : null;
        if (empty($name)) {
            return null;
        }

        /**
         * @var OptionRepository $repo
         */
        $repo = $session->conversation->make(OptionRepository::class);


        if (!$repo->has(ChatOption::class, $name)) {
            return null;
        }

        /**
         * @var ChatOption $option
         */
        $option = $repo->find(ChatOption::class, $name);
        $replies = $option->replies;

        if (empty($replies)) {
            return null;
        }

        if (count($replies) === 1) {
            $reply = current($replies);
        } else {
            $key = array_rand($replies, 1);
            $reply = $replies[$key];
        }

        return $this->reply($self, $dialog, $reply);
    }


    public function reply(Context $self, Dialog $dialog, string $reply) : ? Navigator
    {
        $dialog
            ->say($this->slots)
            ->info(trim($reply));

        if (!isset($this->then)) {
            return $dialog->rewind();
        }
        $then = $this->then;
        unset($this->then);

        return $dialog->app->callContextInterceptor(
            $self,
            $then
        );
    }


}