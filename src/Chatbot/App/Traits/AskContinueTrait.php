<?php


namespace Commune\Chatbot\App\Traits;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 让一个 context 增加默认功能, 要求用户输入任意内容, 然后继续.
 */
trait AskContinueTrait
{
    public function __onAskContinue(Stage $stage) : Navigator
    {
        return $stage
            ->onFallback([Redirector::class, 'next'])
            ->buildTalk()
            ->info('ask.continue')
            ->wait()
            ->hearing()
            ->end([Redirector::class, 'next']);
    }

    protected function askContinueTo(Dialog $dialog, string $next) : Navigator
    {
        return $dialog->goStagePipes(['askContinue', $next]);
    }


    /**
     * 常用在 $stage->onFallback(), 会让用户输入 . 然后继续到下一步.
     *
     * @param string $next
     * @return callable
     */
    public function askContinueOnFallback(string $next) : callable
    {
        return function(Dialog $dialog) use ($next) : Navigator {
            return $this->askContinueTo($dialog, $next);
        };
    }
}