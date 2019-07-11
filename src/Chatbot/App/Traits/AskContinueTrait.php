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
            // 其实输入任意值都会继续
            ->end([Redirector::class, 'next']);
    }

    public function askContinueTo(Dialog $dialog, string ...$next) : Navigator
    {
        array_unshift($next, 'askContinue');
        return $dialog->goStagePipes($next);
    }

    /**
     * 常用在 $stage->onFallback(), 会让用户输入 . 然后继续到下一步.
     *
     * @param string[] $next
     * @return callable
     */
    public function callContinueTo(string ...$next) : callable
    {
        return function(Dialog $dialog) use ($next) : Navigator {
            array_unshift($next, $dialog);
            return call_user_func_array([$this, 'askContinueTo'], $next);
        };
    }
}