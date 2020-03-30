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

use Commune\Message\Blueprint\Abstracted\Comprehension;
use Commune\Message\Blueprint\Convo\ConvoMsg;
use Commune\Message\Prototype\Convo\IText;
use Commune\Shell\Contracts\ShlRequest;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RSRequest implements ShlRequest
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

    public function getScene(): string
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

    public function fetchMessage(): ConvoMsg
    {
        return new IText($this->data);
    }

    public function fetchMessageId(): string
    {
        return $this->messageId ?? $this->messageId = strval(time());
    }

    public function fetchUserId(): string
    {
        return $this->config->userId;
    }

    public function fetchComprehension(): ? Comprehension
    {
        return null;
    }

    public function fetchSessionId(): ? string
    {
        return null;
    }


}