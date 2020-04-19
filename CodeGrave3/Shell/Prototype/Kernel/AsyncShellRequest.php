<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Kernel;

use Commune\Framework\Blueprint\Comprehension;
use Commune\Message\Blueprint\Message;
use Commune\Message\Predefined\Event\AsyncShellReqEvt;
use Commune\Shell\Contracts\ShellRequest;
use Commune\Shell\Contracts\ShellResponse;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AsyncShellRequest implements ShellRequest, HasIdGenerator
{
    use IdGeneratorHelper;


    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var string
     */
    protected $userId;

    /*------- cached -------*/

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var string
     */
    protected $messageId;

    public function __construct(ShellResponse $response)
    {
        $this->chatId = $response->getChatId();
        $this->userId = $response->getUserId();
        $this->traceId = $response->getTraceId();
    }

    public function isStateless(): bool
    {
        return false;
    }

    public function validate(): bool
    {
        return true;
    }

    public function getBrief(): string
    {
        return static::class;
    }

    public function getLogContext(): array
    {
        return [
            'chtId' => $this->getChatId(),
            'traceId' => $this->getTraceId(),
            'usrId' => $this->getUserId(),
        ];
    }

    public function getInput()
    {
        return $this->getMessage();
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getUuid(): string
    {
        return $this->getMessageId();
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getMessageId(): string
    {
        return $this->messageId ?? $this->messageId = $this->createUuId();
    }

    public function getSceneId(): ? string
    {
        return null;
    }

    public function getSceneEnv(): array
    {
        return [];
    }

    public function getMessage(): Message
    {
        return $this->message ?? $this->message = new AsyncShellReqEvt();
    }

    public function getComprehension(): ? Comprehension
    {
        return null;
    }

    public function getSessionId(): ? string
    {
        return null;
    }


}