<?php


namespace Commune\Chatbot\App\Platform\Swoole;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\App\Platform\ConsoleConfig;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Predefined\SimpleConsoleLogger;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;
use Swoole\Server;

class SwooleUserMessageRequest implements MessageRequest, HasIdGenerator
{
    use IdGeneratorHelper;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var ConsoleConfig
     */
    protected $config;

    /**
     * @var int
     */
    protected $fd;

    /*----- cached ----0*/

    /**
     * @var Message
     */
    protected $message;


    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var ConversationMessage[]
     */
    protected $buffers = [];

    /**
     * SwooleUserMessageRequest constructor.
     * @param Server $server
     * @param int $fd
     * @param string $data
     * @param ConsoleConfig|null $config
     */
    public function __construct(
        Server $server,
        int $fd,
        string $data,
        ConsoleConfig $config = null
    )
    {
        $this->data = $data;
        $this->server = $server;
        $this->config = $config;
        $this->fd = $fd;
        $this->config = $config ?? new ConsoleConfig();
    }

    /**
     * @return int
     */
    public function getFd()
    {
        return $this->fd;
    }

    public function generateMessageId(): string
    {
        return $this->createUuId();
    }

    public function getChatbotUserId(): string
    {
        return $this->config->chatbotUserId;
    }

    public function getPlatformId(): string
    {
        return SwooleConsoleServer::class;
    }

    public function fetchMessage(): Message
    {
        return $this->message
            ?? $this->message = new Text($this->data);
    }

    public function fetchMessageId(): string
    {
        return $this->messageId
            ?? $this->messageId = $this->createUuId();
    }

    public function fetchTraceId(): string
    {
        return $this->fetchMessageId();
    }

    public function fetchUserId(): string
    {
        return $this->config->consoleUserId;
    }

    public function fetchUserName(): string
    {
        return $this->config->userName;
    }

    public function fetchUserData(): array
    {
        return [];
    }

    public function bufferMessageToChat(ConversationMessage $message): void
    {
        $this->buffers[] = $message;
    }

    public function flushChatMessages(): void
    {
        while ($message = array_shift($this->buffers)) {
            $this->write($message->getMessage());
        }
    }

    protected function write(Message $msg) : void
    {
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

            $this->server->send(
                $this->fd,
                SimpleConsoleLogger::wrapMessage(
                    $style,
                    $msg->getText()
                )
                . PHP_EOL
            );
        } else {
            $this->server->send($this->fd, $msg->getText() . PHP_EOL);
        }
    }

    public function finishRequest(): void
    {
    }


}