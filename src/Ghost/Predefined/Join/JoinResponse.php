<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Predefined\Join;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\ACodeContext;
use Commune\Protocals\HostMsg\Convo\ContextMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 接受到加入其它会话的申请.
 *
 * @property-read string $session
 * @property-read string $userId
 * @property-read string $userName
 * @property-read string $fromApp
 * @property-read string $fromSession
 *
 * @property bool|null $confirmed;
 *
 */
class JoinResponse extends ACodeContext
{
    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([

            // context 的优先级. 若干个语境在 blocking 状态中, 根据优先级决定谁先恢复.
            'priority' => 1,

            // context 的默认参数名, 类似 url 的 query 参数.
            // query 参数值默认是字符串.
            // query 参数如果是数组, 则定义参数名时应该用 [] 做后缀, 例如 ['key1', 'key2', 'key3[]']
            'queryNames' => [
                'session',
                'userId',
                'userName',
                'fromApp',
                'fromSession',
            ],

            // memory 记忆体的默认值.
            'memoryAttrs' => [

                'confirmation' => null,
            ],

        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->await()
                    ->withSlots([
                        'userId' => $this->userId,
                        'userName' => $this->userName,
                        'fromApp' => $this->fromApp
                    ])
                    ->askConfirm(JoinLang::APPLY_ASK);

            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isPositive()
                    ->then(function(Dialog $dialog) {
                        return $this->receive($dialog, true);
                    })
                    ->isNegative()
                    ->then(function(Dialog $dialog) {
                        return $this->receive($dialog, false);
                    })
                    ->end();
            })
            ->onCancel('cancel');
    }

    public function receive(Dialog $dialog, bool $prove) : Operator
    {
        $this->confirmed = $prove;

        // 路由赋值.
        if ($prove) {
            $storage = $dialog->cloner->storage;
            $routes = $storage->shellSessionRoutes;
            $routes[$this->fromApp] = $this->fromSession;
            $storage->shellSessionRoutes = $routes;
        }

        // 将自己发送回申请的 session.
        $dialog->cloner->dispatcher->yieldContext(
            $this->fromSession,
            $this,
            ContextMsg::MODE_FULFILL
        );

        $lang = $prove ? JoinLang::APPLY_PROVE : JoinLang::APPLY_REJECT;
        return $dialog
            ->send()
            ->info($lang)
            ->over()
            ->fulfill();
    }

    public function __on_cancel(StageBuilder $stage) : StageBuilder
    {
        return $stage->always(function(Dialog $dialog) {
            $dialog->cloner
                ->dispatcher
                ->yieldContext(
                    $this->fromSession,
                    $this,
                    ContextMsg::MODE_CANCEL
                );

            return $dialog
                ->send()
                ->notice(JoinLang::APPLY_CANCEL)
                ->over()
                ->cancel();
        });
    }


}