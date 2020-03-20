<?php

namespace Commune\Chatbot\Framework\Bootstrap;

use Commune\Chatbot\Blueprint\Conversation\Chat;
use Commune\Chatbot\Blueprint\Conversation\ConversationLogger;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Conversation\Renderer;
use Commune\Chatbot\Blueprint\Conversation\User;
use Commune\Chatbot\Contracts\ClientFactory;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Emotion\Feeling;
use Commune\Chatbot\OOHost\Session\Driver as SessionDriver;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Container\ContainerContract;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Chatbot\Contracts\ExceptionReporter;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\ChatKernel;
use Commune\Chatbot\Blueprint\Conversation\Speech;

use Commune\Chatbot\Blueprint\Exceptions\ChatbotLogicException;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Support\SoundLike\SoundLikeInterface;
use Psr\Log\LoggerInterface;

/**
 * 校验当前必须要的绑定.
 *
 * Class ContractsValidator
 * @package Commune\Chatbot\Framework\Bootstrap
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ContractsValidator implements Bootstrapper
{

    /**
     * 默认要在 process 容器中绑定的组件.
     * @var array
     */
    protected $processContracts = [
        // 系统组件的自我绑定
        Application::class,
        ChatServer::class,
        // 系统日志
        LoggerInterface::class,
        ConsoleLogger::class,
        // 必须绑定的配置
        ChatbotConfig::class,
        // 内核绑定.
        ChatKernel::class,
        ExceptionReporter::class,
        OptionRepository::class,
        // 多请求复用的组件.
        EventDispatcher::class,
        Renderer::class,
        Translator::class,
        SoundLikeInterface::class,
        // host
        Feeling::class,
    ];

    /**
     * 默认在 conversation 容器中绑定的组件.
     * @var array
     */
    protected $conversationContracts = [
        // 日志
        ConversationLogger::class,
        // 有IO 开销, 考虑IO非阻塞实现的
        CacheAdapter::class,
        ClientFactory::class,
        // conversation
        IncomingMessage::class,
        User::class,
        Speech::class,
        Chat::class,
        NLU::class,
        // host
        SessionDriver::class,
        Session::class,
        Hearing::class,
    ];

    public function bootstrap(Application $app): void
    {
        $this->validateContracts($app);
    }

    /**
     * 检查系统默认的组件是否已经正确绑定了.
     * @param Application $app
     */
    protected function validateContracts(Application $app)
    {
        $logger = $app->getConsoleLogger();

        $logger->debug("check worker process contracts has been bound correctly");
        foreach ($this->processContracts as $name) {
            $this->assertBound($logger, $app->getProcessContainer(), $name);
        }

        $logger->debug("check conversation contracts has been bound correctly");
        foreach ($this->conversationContracts as $name) {
            $this->assertBound($logger, $app->getConversationContainer(), $name);
        }

    }

    protected function assertBound(
        LoggerInterface $logger,
        ContainerContract $container,
        string $abstract
    ) : void
    {
        // 如果conversation 的contract 绑定到了 base里, 至少报一个warning
        if (! $container->bound($abstract)) {
            $logger->warning("chatbot contract abstract $abstract not bound at container " . get_class($container));
        }

        // 如果base 里都没有绑定, 则中断进程.
        if (! $container->has($abstract)) {
            throw new ChatbotLogicException("chatbot contract abstract $abstract not bound at container " . get_class($container));
        }
    }




}