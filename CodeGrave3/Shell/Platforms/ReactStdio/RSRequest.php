<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Platforms\ReactStdio;

use Commune\Framework\Blueprint\Comprehension;
use Commune\Message\Blueprint\Message;
use Commune\Message\Predefined\IText;
use Commune\Shell\Contracts\ShellRequest;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RSRequest implements ShellRequest
{
    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var RSServiceProvider
     */
    protected $config;

    /**
     * @var string
     */
    protected $data;

    /*----- cached -----*/

    protected $messageId;

    public function validate(): bool
    {
        return true;
    }

    public function getBrief(): string
    {
        // TODO: Implement getBrief() method.
    }

    public function getLogContext(): array
    {
        return [];
    }

    public function getSceneId(): string
    {
        return '';
    }

    public function getSceneEnv(): array
    {
        return [];
    }

    public function getInput()
    {
        return $this->data;
    }

    public function getMessage(): Message
    {
        return new IText($this->data);
    }

    public function getMessageId(): string
    {
        return $this->messageId ?? $this->messageId = strval(time());
    }

    public function getUserId(): string
    {
        return $this->config->userId;
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