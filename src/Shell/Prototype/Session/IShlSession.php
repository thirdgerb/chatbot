<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Session;

use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;
use Commune\Framework\Blueprint\Session\SessionLogger;
use Commune\Framework\Blueprint\Session\SessionStorage;
use Commune\Framework\Contracts;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Blueprint\Intercom\ShellOutput;
use Commune\Framework\Prototype\Session\ASession;
use Commune\Message\Blueprint\Message;
use Commune\Shell\Blueprint\Session\ShlSession;
use Commune\Shell\Blueprint\Session\ShlSessionStorage;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShlSession extends ASession implements ShlSession, HasIdGenerator
{
    use IdGeneratorHelper;

    const INJECTABLE_PROPERTIES = [
        'cache' => Contracts\Cache::class,
        'messenger' => Contracts\Messenger::class,
        'storage' => ShlSessionStorage::class,
        'logger' => SessionLogger::class,
        'request' => ShlRequest::class,
        'response' => ShlResponse::class,
        'shell' => Shell::class,
        'shellInput' => ShellInput::class,
        'ghostInput' => GhostInput::class
    ];

    /**
     * @var ShellOutput[]
     */
    protected $outputs = [];

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var ShellInput
     */
    protected $shellInput;

    /*------ i/o ------*/
    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }


    /**
     * 生成 chatId
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chatId
            ?? $this->chatId = (
                $this->request->getChatId()
                ?? sha1(
                    'shell:'
                    . $this->shell->getShellName()
                    . ':user:'
                    . $this->request->getUserId()
                )
            );
    }

    public function getStorage(): SessionStorage
    {
        return $this->storage;
    }

    public function getLogger(): SessionLogger
    {
        return $this->logger;
    }

    public function output(Message $message): void
    {
        $this->outputs[] = $this->shellInput->output($message);
    }


    public function addShellOutputs(array $outputs): void
    {
        $this->outputs = array_merge($this->outputs, $outputs);
    }

    public function getShellOutputs(): array
    {
        return $this->outputs;
    }

    /*------ finish ------*/

    protected function flush(): void
    {
        $this->outputs = [];
    }

    protected function save(): void
    {
    }


}