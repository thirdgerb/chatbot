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
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sessionId
 * @property-read bool $async
 * @property-read int $errcode
 * @property-read string $errmsg
 * @property-read InputMsg $input
 */
class IShellInputResponse extends AbsMessage implements ShellInputResponse
{
    public static function stub(): array
    {
        return [
            'sessionId' => '',
            'async' => false,
            'errcode' => 0,
            'errmsg' => '',
            'input' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'input' => IInputMsg::class,
        ];
    }

    public function isEmpty(): bool
    {
        return false;
    }


    public function fill(array $data): void
    {
        if (empty($data['errmsg'])) {
            $errcode = $data['errcode'] ?? 0;
            $data['errmsg'] = AppResponse::DEFAULT_ERROR_MESSAGES[$errcode];
        }

        parent::fill($data);
    }

    /*-------- request --------*/

    public function getTraceId(): string
    {
        return $this->getInput()->getMessageId();
    }

    public function getSessionId(): string
    {
        $sessionId = $this->sessionId;
        return empty($sessionId)
            ? $this->getInput()->getSessionId()
            : $sessionId;
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

    public function getInput(): InputMsg
    {
        return $this->input;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }


    /*-------- protocal --------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }


}