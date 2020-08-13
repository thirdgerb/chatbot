<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Message\Host\SystemInt\DialogStageEventInt;
use Commune\Protocals\HostMsg\DefaultIntents;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CancelStage extends AStageDef
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '退出语境',
            'desc' => '退出',

            'contextName' => '',
            'stageName' => '',
            'asIntent' => null,

            'events' => [],
            'ifRedirect' => null,
        ];
    }

    public function onActivate(Activate $dialog): Operator
    {
        return $this->cancel($dialog);
    }

    public function onReceive(Receive $dialog): Operator
    {
        return $this->cancel($dialog);
    }

    public function onRedirect(Dialog $prev, Ucl $current): ? Operator
    {
        return null;
    }

    public function onResume(Resume $dialog): ? Operator
    {
        return $this->cancel($dialog);
    }

    protected function cancel(Dialog $dialog) : Operator
    {
        $intent = DialogStageEventInt::instance(
            DefaultIntents::GUEST_NAVIGATE_CANCEL,
            $dialog->ucl->getStageFullname()
        );

        return $dialog
            ->send()
            ->message($intent)
            ->over()
            ->cancel();
    }


}