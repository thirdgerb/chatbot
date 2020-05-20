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
use Commune\Support\Struct\Struct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IOutputMsg extends AIntercomMsg implements OutputMsg
{

    protected $transferNoEmptyRelations = false;

    protected $transferNoEmptyData = true;

    public function __construct(
        HostMsg $message,
        string $traceId,
        string $guestId,
        string $messageId = null,
        string $convoId = null,
        string $sessionId = null,
        string $guestName = null,
        int $deliverAt = 0
    )
    {
        $data = [
            'messageId' => empty($messageId)
                ? $this->createUuId()
                : $messageId,
            'traceId' => $traceId,
            'sessionId' => $sessionId ?? '',
            'convoId' => $convoId ?? '',
            'guestId' => $guestId,
            'guestName' => $guestName ?? '',
            'message' => $message,
            'deliverAt' => $deliverAt,
            'createdAt' => time(),
        ];
        parent::__construct($data);
    }


    public static function stub(): array
    {
        return [
            'messageId' => '',
            'traceId' => '',
            'sessionId' => '',
            'convoId' => '',
            'guestId' => '',
            'guestName' => '',
            'message' => new IText(),
            'deliverAt' => 0,
            'createdAt' => time(),
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['message'],
            $data['traceId'] ?? '',
            $data['guestId'] ?? '',
            $data['messageId'] ?? null,
            $data['convoId'] ?? null,
            $data['sessionId'] ?? null,
            $data['guestName'] ?? null,
            $data['deliverAt'] ?? 0
        );
    }

    public function derive(HostMsg $message, HostMsg ...$messages): array
    {
        array_unshift($messages, $message);

        return array_map(function(HostMsg $message) {
            return $this->divide($message);
        }, $messages);
    }

}