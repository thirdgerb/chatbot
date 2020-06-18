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

use Commune\Protocals\Intercom\OutputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;
use Commune\Support\Utils\TypeUtils;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $sessionId
 * @property-read string $traceId
 * @property-read int $errcode
 * @property-read string $errmsg
 * @property-read OutputMsg[] $outputs
 */
class IShellOutputResponse extends AbsMessage implements ShellOutputResponse
{

    public static function stub(): array
    {
        return [
            'sessionId' => '',
            'traceId' => '',
            'errcode' => null,
            'errmsg' => '',
            'outputs' => []
        ];
    }

    public static function relations(): array
    {
        return [
            'outputs[]' => OutputMsg::class,
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['sessionId', 'traceId'])
            ?? TypeUtils::issetFields($data, ['errcode'])
            ?? parent::validate($data);
    }

    /*------ message ------*/

    public function isEmpty(): bool
    {
        return false;
    }

    /*------ protocal ------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

    /*------ request ------*/

    public function getOutputs(): array
    {
        return $this->outputs;
    }

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
        $errmsg = $this->errmsg;

        return empty($errmsg)
            ? AppResponse::DEFAULT_ERROR_MESSAGES[$this->errcode]
            : $errmsg;
    }

    public function isSuccess(): bool
    {
        return $this->errcode < AppResponse::FAILURE_CODE_START;
    }


}