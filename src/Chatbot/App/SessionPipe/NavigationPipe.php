<?php


namespace Commune\Chatbot\App\SessionPipe;


use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;
use Commune\Chatbot\App\Components\Predefined;

class NavigationPipe implements SessionPipe
{
    protected $navigationIntents = [
        Predefined\Navigation\HomeInt::class,
        Predefined\Navigation\BackwardInt::class,
        Predefined\Navigation\QuitInt::class,
        Predefined\Navigation\CancelInt::class,
        Predefined\Navigation\RepeatInt::class,
        Predefined\Navigation\RestartInt::class,
    ];


    public function handle(Session $session, \Closure $next): Session
    {
        $navigation = $this->navigationIntents;

        if (empty($navigation)) {
            return $next($session);
        }

        // 检查matched
        $intent = $session->getMatchedIntent();
        $message = $session->incomingMessage->message;
        if (!isset($intent) && !$message instanceof VerboseMsg) {
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