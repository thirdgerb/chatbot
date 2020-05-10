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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Message\Host\Convo\IContextMsg;
use Commune\Protocals\Host\Convo\ContextMsg;
use Commune\Protocals\Intercom\YieldInput;
use Commune\Support\Struct\Struct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IYieldInput extends IGhostInput implements YieldInput
{

    public function __construct(ContextMsg $contextMsg, string $clonerId, string $shellName, string $shellId, string $senderId, string $messageId = null, array $moreInfo = [
        //'batchId' => 'id',
        //'sceneId' => '',
        //'env' => [],
        //'senderName' => '',
        //'guestId' => '',
        //'deliverAt' => 0,
        //'createdAt' => 0
    ], $comprehension = null)
    {
        parent::__construct(
            $contextMsg,
            $clonerId,
            null,
            $shellName,
            $shellId,
            $senderId,
            $messageId,
            $moreInfo,
            $comprehension
        );
    }

    public static function stub(): array
    {
        $data = parent::stub();
        $data['message'] = new IContextMsg();
        return $data;
    }


    public static function create(array $data = []): Struct
    {
        return new static(
            $data['message'] ?? null,
            $data['cloneId'] ?? '',
            $data['shellName'] ?? '',
            $data['shellId'] ?? '',
            $data['senderId'] ?? '',
            $data['messageId'] ?? '',
            $data,
            $data['comprehension'] ?? null
        );
    }

    public function toContext(Cloner $cloner): Context
    {
        /**
         * @var ContextMsg $message
         */
        $message = $this->message;
        return $message->toContext($cloner);
    }


}