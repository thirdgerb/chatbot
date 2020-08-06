<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Predefined\Join;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\ACodeContext;
use Commune\Message\Host\SystemInt\SessionSyncInt;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $session
 * @property-read string $fallback
 *
 * @property JoinResponse $response
 */
class JoinRequest extends ACodeContext
{
    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'queryNames' => [
                'session',
                'fallback',
            ],

          // memory 记忆体的默认值.
            'memoryAttrs' => [
                'response' => null,
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
                $cloner = $dialog->cloner;
                $scene = $cloner->scene;

                $currentSession = $cloner->getSessionId();
                if ($currentSession === $this->session) {
                    return $dialog->send()
                        ->error(JoinLang::ERROR_SESSION_SAME)
                        ->over()
                        ->cancel();
                }

                if (!$cloner->isClonerExists($this->session)) {
                    return $dialog->send()
                        ->error(JoinLang::ERROR_SESSION_NOT_FOUND)
                        ->over()
                        ->cancel();
                }

                $join = JoinResponse::genUcl([
                   'session' => $this->session,
                    'userId' => $scene->userId,
                    'userName' => $scene->userName,
                    'fromApp' => $scene->fromApp,
                    'fromSession' => $cloner->getSessionId(),
               ]);

                $this->response = $context = $join->findContext($dialog->cloner);
                $fallback = $this->fallback;
                $fallback = empty($fallback)
                    ? null
                    : Ucl::decode($fallback);

                return $dialog
                    ->yieldTo(
                        $this->session,
                        $context,
                        $fallback
                    );
            })
            ->onEvent(
                Dialog::CALLBACK,
                function(Dialog\Resume\Callback $dialog) {
                    $response = $this->response;

                    if ($response->confirmed) {

                        $dialog
                            ->send()
                            ->info(JoinLang::REPLY_PROVED)
                            ->message(SessionSyncInt::instance($this->session))
                            ->over();
                        // 后续的消息不发送给用户了.
                        $dialog->cloner->silence(true);
                        return $dialog->fulfill();
                    }

                    return $dialog
                        ->send()
                        ->info(JoinLang::REPLY_REJECTED)
                        ->over()
                        ->fulfill();
                }
            )
            ->onCancel('cancel');
    }

    public function __on_cancel(StageBuilder $stage) : StageBuilder
    {
        return $stage->always(
            $stage
                ->dialog
                ->send()
                ->notice(JoinLang::REPLY_CANCELED)
                ->over()
                ->fulfill()
        );
    }

}