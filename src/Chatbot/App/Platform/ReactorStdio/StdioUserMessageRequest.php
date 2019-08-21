<?php

/**
 * Class UserMessageHandler
 * @package Commune\Chatbot\App\Platform\ReactorStdio
 */

namespace Commune\Chatbot\App\Platform\ReactorStdio;

use Clue\React\Stdio\Stdio;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\App\Platform\ConsoleConfig;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Conversation\MessageRequestHelper;
use Commune\Chatbot\Framework\Predefined\SimpleConsoleLogger;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Chatbot\Blueprint\Message\QA\Question;

class StdioUserMessageRequest implements MessageRequest, HasIdGenerator
{
    use MessageRequestHelper;

    /**
     * @var string|Message
     */
    protected $line;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var Stdio
     */
    protected $stdio;

    /**
     * @var ConversationMessage[]
     */
    protected $buffers = [];

    /**
     * @var ConsoleConfig
     */
    protected $config;

    /**
     *
     * StdioUserMessageRequest constructor.
     * @param string|Message $line
     * @param Stdio $stdio
     * @param ConsoleConfig $config
     */
    public function __construct($line, Stdio $stdio, ConsoleConfig $config)
    {
        $this->line = $line;
        $this->stdio = $stdio;
        $this->config = $config;
    }

    public function getInput()
    {
        return $this->line;
    }


    public function generateMessageId(): string
    {
        return $this->createUuId();
    }

    public function getPlatformId(): string
    {
        return StdioServer::class;
    }

    public function fetchMessage(): Message
    {
        if ($this->line instanceof Message) {
            return $this->line;
        }
        return $this->message ?? $this->message = new Text($this->line);
    }

    public function fetchUserId(): string
    {
        return $this->config->consoleUserId;
    }

    public function fetchUserName(): string
    {
        return $this->config->consoleUserName;
    }

    public function fetchUserData(): array
    {
        return [];
    }

    public function bufferConversationMessage(ConversationMessage $message): void
    {
        $this->buffers[] = $message;
    }

    public function flushChatMessages(): void
    {
        while ($message = array_shift($this->buffers)) {
            $this->write($message->getMessage());
        }
        $this->buffers = [];
    }

    protected function write(Message $msg) 
    {
        // 顺手加一个自动完成
        if ($msg instanceof Question) {
            $this->stdio
                ->getReadline()
                ->setAutocomplete(function () use ($msg) {
                    return $msg->suggestions();
                });

        }
        // 显示一下颜色.
        if ($msg instanceof VerboseMsg) {

                switch ($msg->getLevel()) {
                    case VerboseMsg::DEBUG:
                        $style = 'debug';
                        break;
                    case VerboseMsg::INFO:
                        $style = 'info';
                        break;
                    case VerboseMsg::WARN:
                        $style = 'warning';
                        break;
                    default:
                        $style = 'error';
                }

                $this->stdio->write(
                    SimpleConsoleLogger::wrapMessage(
                        $style,
                        $msg->getText()
                    )
                    . PHP_EOL
                );

        } else {

            $this->stdio->write($msg->getText() . PHP_EOL);
        }
        $this->stdio->write(PHP_EOL);
    }

}