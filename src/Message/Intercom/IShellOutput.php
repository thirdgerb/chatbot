<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Intercom;

use Commune\Message\Host\Convo\IVerbalMsg;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\ShellOutput;
use Commune\Support\Message\AbsMessage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellOutput extends AbsMessage implements ShellOutput
{
    public static function stub(): array
    {
        return [
            'messageId' => '',
            'batchId' => '',
            'sceneId' => '',
            'env' => [],

            'message' => new IVerbalMsg(),

            'deliverAt' => $now = round(floatval(microtime(true)), 3),
            'createdAt' => $now,
        ];
    }

    public static function relations(): array
    {
        return [
            'message' => HostMsg::class,
        ];
    }


}