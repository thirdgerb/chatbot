<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\Protocals;

use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sessionId
 * @property-read string $traceId
 * @property-read int $errcode
 * @property-read string $errmsg
 * @property-read OutputMsg[] $outputs
 */
class IGhostResponse extends AbsMessage implements GhostResponse
{
    public static function stub(): array
    {
        return [
            'traceId' => '',
            'sessionId' => '',
            'errcode' => 0,
            'errmsg' => '',
            'outputs' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'outputs[]' => OutputMsg::class,
        ];
    }

    public function fill(array $data): void
    {
        if (empty($data['errmsg'])) {
            $errcode = $data['errcode'] ?? 0;
            $data['errmsg'] = AppResponse::DEFAULT_ERROR_MESSAGES[$errcode];
        }

        parent::fill($data);
    }

    /*-------- message --------*/

    public function isEmpty(): bool
    {
        return false;
    }

    /*-------- request --------*/

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getErrcode(): int
    {
        return $this->errcode;
    }

    public function getErrmsg(): string
    {
        return $this->errmsg;
    }

    public function isSuccess(): bool
    {
        return $this->errcode < AppResponse::FAILURE_CODE_START;
    }

    public function getOutputs(): array
    {
        return $this->outputs;
    }


    /*-------- protocal --------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }


}