<?php


namespace Commune\Chatbot\App\Callables\Intercepers;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\OOHost\Context\Callables\Interceptor;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 拦截器, 检查用户必须是超级管理员.
 * 作为一个拦截器的示范.
 */
class MustBeSupervisor implements Interceptor
{
    public function __invoke(
        Context $self,
        Dialog $dialog
    ): ? Navigator
    {
        $isSupervisor = $dialog->session
            ->conversation
            ->isAbleTo(Supervise::class);

        if ($isSupervisor) {
            return null;
        }

        // 使用默认的拒绝进入消息.
        return $dialog->reject();
    }


}