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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class EntityStage extends AbsStageDef
{

    public static function stub(): array
    {
        return [
            'name' => '',
            'contextName' => '',
            'title' => '',
            'desc' => '',
            'stageName' => '',
            'asIntent' => [],
            'events' => [],
            'ifRedirect' => null,
        ];
    }


    public function onActivate(Activate $dialog): Operator
    {
        return $dialog
            ->await()
            ->askVerbal()
    }

    public function onReceive(Receive $dialog): Operator
    {
        return $dialog
            ->hearing()
            ->isAnswer()
            ->then(function () {

            })
            ->end();
    }

    public function onRedirect(Dialog $prev, Dialog $current): ? Operator
    {
        return null;
    }

    public function onResume(Resume $dialog): ? Operator
    {
        return null;
    }


}