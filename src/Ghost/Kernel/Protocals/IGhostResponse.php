<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Kernel\Protocals;

use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $traceId;
 * @property-read int $errcode
 * @property-read string $errmsg
 * @property-read string $shellName
 * @property-read string $shellId
 * @property-read string $batchId
 * @property-read int $count
 * @property-read bool $tiny
 * @property-read bool $async
 * @property-read IntercomMsg[] $messages
 */
class IGhostResponse extends AbsMessage implements GhostResponse
{

    protected $transferNoEmptyData = true;

    protected $transferNoEmptyRelations = false;

    public static function stub(): array
    {
        return [
            'traceId' => '',
            'errcode' => AppResponse::SUCCESS,
            'errmsg' => '',
            'shellName' => '',
            'shellId' => '',
            'batchId' => '',
            'count' => 0,
            'tiny' => true,
            'async' => true,
            'messages' => []
        ];
    }

    public static function relations(): array
    {
        return [
            'messages[]' => IntercomMsg::class
        ];
    }


    public function __get_errmsg(string $name) : string
    {
        $text = $this->_data[$name] ?? '';

        return empty($text)
            ? (AppResponse::DEFAULT_ERROR_MESSAGES[$this->errcode] ?? '')
            : $text;
    }

    /*------- ghost response -------*/

    public function getMessageCount(): int
    {
        return $this->count;
    }

    public function isTinyResponse(): bool
    {
        return $this->tiny;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }


    public function getBatchId(): string
    {
        return $this->batchId;
    }

    public function getShellName(): string
    {
        return $this->shellName;
    }

    public function getShellSessionId(): string
    {
        return $this->shellId;
    }


    /*------- app response -------*/

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
        return $this->getErrcode() < AppResponse::FAILURE_CODE_START;
    }


    /*------- protocal -------*/

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }


    /*------- protocal -------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }


}