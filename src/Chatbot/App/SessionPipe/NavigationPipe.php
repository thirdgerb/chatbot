<?php


namespace Commune\Chatbot\App\SessionPipe;


use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;
use Commune\Components\Predefined\Intents;

/**
 * 导航管道. 定义在这里的意图会最优先进行匹配, 匹配陈宫给后会调用 IntentMessage::navigate 方法.
 */
class NavigationPipe implements SessionPipe
{
    protected $navigationIntents = [
        Intents\Navigation\HomeInt::class,
        Intents\Navigation\BackwardInt::class,
        Intents\Navigation\QuitInt::class,
        Intents\Navigation\CancelInt::class,
        Intents\Navigation\RepeatInt::class,
        Intents\Navigation\RestartInt::class,
    ];


    public function handle(Session $session, \Closure $next): Session
    {
        $navigation = $this->navigationIntents;

        if (empty($navigation)) {
            return $next($session);
        }

        foreach ($navigation as $intentName) {
            // 匹配到了.
            $intent = $session->getPossibleIntent($intentName);
            if (isset($intent)) {
                return $this->runIntent($intent, $session) ?? $next($session);
            }
        }
        return $next($session);
    }

    protected function runIntent(IntentMessage $intent, Session $session) : ? Session
    {
        $navigator = $intent->navigate($session->dialog);

        // 导航类
        if (isset($navigator)) {
            $session->handle(
                $session->incomingMessage->message,
                $navigator
            );
            return $session;
        }
        return null;
    }


}