<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo\Api;

use Commune\Contracts\Messenger\MessageDB;
use Commune\Message\Host\Convo\IApiMsg;
use Commune\Protocols\HostMsg\DefaultApi;


/**
 * 请求消息的 api 调用.
 *
 * @see MessageDB
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string|null $sessionId
 * @property-read string|null $batchId
 * @property-read string|null $creatorId
 * @property-read int|null $deliverAfter
 * @property-read int|null $createdAfter
 * @property-read int|null $afterId
 * @property-read int $offset
 * @property-read int $limit
 */
class MessagesQuery extends IApiMsg
{

    public function instance(array $data) : self
    {
        unset($data['api']);
        return new static($data);
    }

    public static function stub(): array
    {
        return [
            'api' => DefaultApi::SYSTEM_MESSAGES_QUERY,
            'sessionId' => null,
            'batchId' => null,
            'creatorId' => null,
            'deliverAfter' => null,
            'createdAfter' => null,
            'afterId' => null,
            'offset' => 0,
            'limit' => 10
        ];
    }

    public function __get_params(string $name) : array
    {
        $data = $this->_data;
        unset($data['api']);
        return $data;
    }

}