<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Messenger\Broadcaster;

use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Kernel\Protocals\IShellOutputRequest;


/**
 * 一个极简的广播协议.
 * 实际项目建议还是使用真正的广播服务.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BroadcastProtocal
{
    const ACT_PUB = 'pub';
    const ACT_SUB = 'sub';
    const ACT_SEND = 'send';
    const ACT_ACK = 'ack';

    const ACTION = 'act';
    const SHELL_ID = 'shl';
    const SESSION_ID = 'ssi';
    const BATCH_ID = 'bti';
    const TRACE_ID = 'tci';
    const CREATOR_ID = 'cti';
    const CREATOR_NAME = 'ctn';

    public static function serializePublish(
        string $shellId,
        string $sessionId,
        string $batchId,
        string $traceId,
        string $creatorId,
        string $creatorName
    ) : string
    {
        return json_encode([
            self::ACTION => self::ACT_PUB,
            self::SHELL_ID => $shellId,
            self::SESSION_ID => $sessionId,
            self::BATCH_ID => $batchId,
            self::TRACE_ID => $traceId,
            self::CREATOR_ID => $creatorId,
            self::CREATOR_NAME => $creatorName
        ]);
    }

    public static function serializeSubscribe(
        string $shellId,
        string $sessionId = null
    ) : string
    {
        return json_encode([
            self::ACTION => self::ACT_SUB,
            self::SHELL_ID => $shellId,
            self::SESSION_ID => $sessionId ?? ''
        ]);
    }

    public static function serializeSend(
        string $sessionId,
        string $batchId,
        string $traceId
    ) : string
    {
        return json_encode([
            self::ACTION => self::ACT_SEND,
            self::SESSION_ID => $sessionId,
            self::BATCH_ID => $batchId,
            self::TRACE_ID => $traceId
        ]);
    }


    public static function unserializePublish(string $publish) : ? ShellOutputRequest
    {
        $unserialize = self::unserialize($publish);
        if (!is_array($unserialize)) {
            return null;
        }

        if (!self::isAction($unserialize, self::ACT_SEND)) {
            return null;
        }

        $sessionId = $unserialize[self::SESSION_ID] ?? '';
        $traceId = $unserialize[self::TRACE_ID] ?? '';
        $batchId = $unserialize[self::BATCH_ID] ?? '';
        $creatorId = $unserialize[self::CREATOR_ID] ?? '';
        $creatorName = $unserialize[self::CREATOR_NAME] ?? '';

        if (
            empty($sessionId)
            || empty($traceId)
            || empty($batchId)
        ) {
            return null;
        }

        return IShellOutputRequest::asyncInstance(
            $sessionId,
            $traceId,
            $batchId
        );
    }

    public static function serializeAck() : string
    {
        return json_encode([self::ACTION => self::ACT_ACK]);
    }

    public static function unserialize(string $info) : ? array
    {
        $decoded = json_decode($info, true);
        return empty($decoded)
            ? null
            : $decoded;
    }

    public static function isAction(array $data, string $action) : bool
    {
        return ($data[self::ACTION] ?? '') === $action;
    }

}