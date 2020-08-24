<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Command;
use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;


/**
 * 重定向到 stage, 做成命令, 不占用选项.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 * @property-read string $stage
 */
class StageCmdDef extends AContextCmdDef
{

    public static function instance(
        string $stageName,
        string $desc,
        string $cmd = null
    ) : self
    {
        return new static([
            'desc' => $desc,
            'signature' => $cmd ?? $stageName,
            'stage' => $stageName
        ]);
    }



    public static function stub(): array
    {
        return [
            'desc' => '',
            'signature' => '',
            'stage' => ''
        ];
    }

    public function handle(
        Dialog $dialog,
        CommandMsg $message
    ): ? Operator
    {
        return $dialog->goStage($this->stage);
    }


}