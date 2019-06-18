<?php

/**
 * Class ChatbotApp
 * @package Commune\Chatbot\Framework
 */

namespace Commune\Chatbot\Framework;


// interface
use Commune\Chatbot\Blueprint\Conversation\ConversationContainer;
use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Predefined\SimpleConsoleLogger;
use Commune\Chatbot\Framework\Providers;
use Commune\Chatbot\Config\Host\OOHostConfig;
use Commune\Chatbot\OOHost\OOHostServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Chatbot\Blueprint\Kernel;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Application as Blueprint;

// bootstrapper
use Commune\Chatbot\Framework\Bootstrap;

// framework
use Commune\Chatbot\Framework\Exceptions\BootingException;

// config
use Commune\Chatbot\Config\ChatbotConfig;

// impl
use Commune\Chatbot\Framework\Conversation\ConversationImpl;
use Commune\Container\IlluminateAdapter;
use Illuminate\Container\Container;
use Psr\Log\LoggerInterface;

/**
 * Class ChatbotApp
 * @package Commune\Chatbot\Framework
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ChatApp implements Blueprint
{

    /*-------- 配置 --------*/

    /**
     * @var string[]
     */
    protected $bootstrappers = [
        // 打招呼
        Bootstrap\WelcomeToUserChatbot::class,
        // 加载预定义的配置.
        Bootstrap\LoadConfiguration::class,
        // 注册用户的 service provider
        Bootstrap\RegisterProviders::class,
        // 注册组件
        Bootstrap\LoadComponents::class,
        // 检查必要的服务是否注册完成.
        Bootstrap\ContractsValidator::class,
    ];

    /*-------- 内存缓存 --------*/

    /**
     * @var bool
     */
    protected $available = true;

    /**
     * @var bool
     */
    protected $reactorBooted = false;

    /**
     * @var ChatbotConfig
     */
    protected $config;

    /**
     * @var ContainerContract
     */
    protected $reactorContainer;

    /**
     * @var ContainerContract
     */
    protected $conversationContainer;

    /**
     * @var ConsoleLogger
     */
    protected $consoleLogger;

    /**
     * @var string[]
     */
    protected $registeredProviders = [];

    /**
     * @var ServiceProvider[]
     */
    protected $reactorProviders = [];

    /**
     * @var ServiceProvider[]
     */
    protected $conversationProviders = [];

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * ChatbotApp constructor.
     * @param array $config
     * @param ContainerContract|null $reactorContainer
     * @param ConsoleLogger|null $consoleLogger
     */
    public function __construct(
        array $config,
        ContainerContract $reactorContainer = null,
        ConsoleLogger $consoleLogger = null
    )
    {

        // 默认配置
        $this->config = new ChatbotConfig($config);

        // 默认的常量
        if (!defined('CHATBOT_DEBUG')) {
            define('CHATBOT_DEBUG', boolval($this->config->debug));
        }

        // 默认的组件
        $this->reactorContainer = $reactorContainer
            ?? new IlluminateAdapter(new Container());

        $this->consoleLogger = $consoleLogger
            ?? new SimpleConsoleLogger();

        // 创建会话容器.
        $this->conversationContainer = new ConversationImpl($this->reactorContainer);
        $this->baseBinding();
        $this->baseRegister();
    }

    public function getConsoleLogger(): ConsoleLogger
    {
        return $this->consoleLogger;
    }

    /**
     * @param string|ServiceProvider $provider
     */
    public function registerReactorService($provider): void
    {
        $provider = $this->parseProvider(
            $provider,
            $this->reactorContainer
        );

        if (isset($provider)) {
            $this->reactorProviders[] = $provider;
        }
    }


    public function registerConversationService($providerName): void
    {
        $provider = $this->parseProvider(
            $providerName,
            $this->conversationContainer
        );

        if (isset($provider)) {
            $this->conversationProviders[] = $provider;
        }
    }

    /**
     * @param string|ServiceProvider $providerName
     * @param ContainerContract $container
     * @return ServiceProvider
     */
    protected function parseProvider(
        $providerName,
        ContainerContract $container
    ) : ? ServiceProvider
    {
        if (is_string($providerName)) {

            if (isset($this->registeredProviders[$providerName])) {
                $this->consoleLogger
                    ->warning("try to register reactor provider $providerName which already loaded");
                return null;
            }

            /**
             * @var ServiceProvider $provider
             */
            $provider = new $providerName($container);

        } else {
            $provider = $providerName;

        }

        if ($provider instanceof ServiceProvider) {

            $provider = $provider->withApp($container);

            $clazz = get_class($provider);
            if (!isset($this->registeredProviders[$clazz])) {
                $provider->register();
                $this->registeredProviders[$clazz] = true;
            }

            return $provider;
        }

        throw new ConfigureException(
            __METHOD__
            . ' only accept class name or instance of '
            . ServiceProvider::class
        );
    }



    public function bootReactor() : Blueprint
    {
        // 不要重复启动.
        if ($this->reactorBooted) {
            return $this;
        }

        $logger = $this->consoleLogger;
        try {
            $logger->info(static::class . ' chatbot reactor booting');


            // 完成各种注册逻辑.
            foreach ($this->bootstrappers as $bootstrapperName) {
                $logger->info("run bootstrapper: $bootstrapperName");
                /**
                 * @var Bootstrap\Bootstrapper $bootstrapper
                 */
                $bootstrapper = (new $bootstrapperName);
                $bootstrapper->bootstrap($this);
            }

            // 初始化容器.
            $logger->info(
                "boot base container: "
                . get_class($this->reactorContainer)
            );

            $logger->info(static::class . ' chatbot reactor boot');
            // baseContainer 执行boot流程.
            foreach ($this->reactorProviders as $provider) {
                $logger->debug("boot provider " . get_class($provider));
                $provider->boot($this->reactorContainer);
            }

            $logger->info(static::class . ' chatbot reactor booted');
            $this->reactorBooted = true;

            return $this;

        } catch (\Throwable $e) {

            $fatal =  new BootingException( $e);
            $logger->critical($fatal);
            //$this->server->close();

            throw $fatal;
        }
    }

    protected function baseBinding() : void
    {
        // 绑定默认组件到容器上.
        $this->consoleLogger->info("self binding....... ");

        // self
        $this->reactorContainer->instance(Blueprint::class, $this);

        // config
        $this->reactorContainer->instance(ChatbotConfig::class, $this->config);
        $this->reactorContainer->instance(OOHostConfig::class, $this->config->host);

        // server
        $this->reactorContainer->instance(ConsoleLogger::class, $this->consoleLogger);

        // kernel
        $this->reactorContainer->singleton(Kernel::class, ChatKernel::class);

    }

    protected function baseRegister() : void
    {
        // reactor
        $this->registerReactorService(Providers\TranslatorServiceProvider::class);
        // host
        $this->registerReactorService(OOHostServiceProvider::class);
        $this->registerReactorService(Providers\LoggerServiceProvider::class);


        // conversation
        $this->registerConversationService(Providers\EventServiceProvider::class);
        $this->registerConversationService(Providers\ConversationalServiceProvider::class);

    }


    public function bootConversation(Conversation $conversation): void
    {
        foreach ($this->conversationProviders as $provider) {
            $provider->boot($conversation);
        }
    }

    /**
     * @return ChatbotConfig
     */
    public function getConfig(): ChatbotConfig
    {
        return $this->config;
    }

    public function getReactorContainer() : ContainerContract
    {
        return $this->reactorContainer;
    }

    public function getConversationContainer() : ConversationContainer
    {
        return $this->conversationContainer;
    }

    public function getKernel(): Kernel
    {
        $this->bootReactor();
        return $this->reactorContainer->make(Kernel::class);
    }

    public function getServer(): ChatServer
    {
        $this->bootReactor();
        return $this->reactorContainer->make(ChatServer::class);
    }



    /*--------- 状态 ---------*/

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @param bool $available
     */
    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

}