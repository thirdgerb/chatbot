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

use Commune\Message\Host\Convo\IText;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $messageId  为空则自动生成.
 * @property-read string $traceId    允许为空
 * @property-read string $shellName
 * @property-read string $batchId    批次ID, 为空则是 messageId
 * @property-read string $sessionId  会话Id, 为空则是 guestId
 * @property-read string $guestId    用户的ID. 不可以为空.
 * @property-read string $guestName  用户的姓名. 可以为空.
 * @property-read int $createdAt     创建时间.
 * @property-read int $deliverAt     发送时间. 默认为0.
 *
 * @property HostMsg $message   输入消息. 不可以为空.
 * @property string $convoId    多轮会话的 ID. 允许为空. 除非客户端有指定的 conversation.
 */
class IOutputMsg extends AIntercomMsg implements OutputMsg
{

    protected $transferNoEmptyRelations = false;

    protected $transferNoEmptyData = true;

    public static function stub(): array
    {
        return [
            'messageId' => '',

            // 不可为空.
            'shellName' => '',

            // 不可为空
            'traceId' => '',

            // 不可为空
            'sessionId' => '',

            // 不可为空
            'batchId' => '',

            'convoId' => '',

            // 不可为空
            'guestId' => '',

            'guestName' => '',

            // 不可为空
            'message' => new IText(),
            'deliverAt' => 0,
            'createdAt' => time(),
        ];
    }

    public function isInvalid(): ? string
    {
        return TypeUtils::requireFields(
            $this->_data,
            ['messageId', 'traceId', 'sessionId', 'batchId', 'guestId', 'convoId', 'message', 'shellName']
        );
    }

    public function getShellName(): string
    {
        return $this->shellName;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }


    public function derive(HostMsg ...$messages): array
    {
        return array_map(function(HostMsg $message) {
            return $this->divide($message);
        }, $messages);
    }

}