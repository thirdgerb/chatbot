<?php

/**
 * Class ChatbotApp
 * @package Commune\Chatbot\Framework
 */

namespace Commune\Chatbot\Framework;


// interface
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\ConversationContainer;
use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Framework\Impl\SimpleConsoleLogger;
use Commune\Chatbot\Config\Children\OOHostConfig;
use Commune\Container\ContainerContract;
use Commune\Chatbot\Blueprint\ChatKernel;
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

/**
 * Class ChatbotApp
 * @package Commune\Chatbot\Framework
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ChatApp implements Blueprint
{
    /**
     * @var ChatApp
     */
    protected static $instance;

    /*-------- 配置属性 --------*/

    /**
     * 启动时运行的加载逻辑
     * 之所以定义在 ChatApp 内, 而不是 Kernel, 因为是进程级的.
     * 必须在响应第一个请求前运行完毕.
     *
     * 可以通过继承 ChatApp 定义自己的启动流程.
     *
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

    /**
     * 默认的 Kernel. 可以重写替换.
     * @var string
     */
    protected $chatKernel = ChatKernelImpl::class;

    /*-------- 内存缓存 --------*/
    /**
     * @var bool
     */
    protected $workerBooted = false;

    /**
     * @var ChatbotConfig
     */
    protected $config;

    /**
     * @var ContainerContract
     */
    protected $processContainer;

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
    protected $configProviders = [];


    /**
     * @var ServiceProvider[]
     */
    protected $processProviders = [];

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
     * @param array|ChatbotConfig $config
     * @param ContainerContract|null $processContainer
     * @param ConsoleLogger|null $consoleLogger
     */
    public function __construct(
        $config,
        ContainerContract $processContainer = null,
        ConsoleLogger $consoleLogger = null
    )
    {
        // 绑定自己作为单例.
        static::$instance = $this;

        // 默认配置
        if (is_array($config)) {
            $config = new ChatbotConfig($config);
        }
        if (!$config instanceof ChatbotConfig) {
            throw new BootingException('config is invalid');
        }
        $this->config = $config;

        // 默认的常量, 只会定义一次. 理论上一个process 也只启动一个chatbot
        if (!defined('CHATBOT_DEBUG')) {
            define('CHATBOT_DEBUG', boolval($this->config->debug));
        }

        // 最好强制进行类型检查. 避免致命的notice
        if (CHATBOT_DEBUG) {
            error_reporting(E_ALL);
        }

        // 默认的组件
        $this->processContainer = $processContainer
            ?? new IlluminateAdapter(new Container());

        $this->consoleLogger = $consoleLogger
            ?? new SimpleConsoleLogger();

        // 创建会话容器.
        $this->conversationContainer = new ConversationImpl($this->processContainer);

        $this->baseBinding();
    }

    public static function getInstance() : Application
    {
        return static::$instance;
    }

    public function getConsoleLogger(): ConsoleLogger
    {
        return $this->consoleLogger;
    }

    public function registerConfigService($provider): void
    {
        $provider = $this->parseProvider(
            $provider,
            $this->processContainer
        );

        if (isset($provider)) {
            if (
                $provider instanceof ServiceProvider
                && !$provider->isProcessServiceProvider()
            ) {
                $name = get_class($provider);
                $this->getConsoleLogger()->warning("register process provider $name which declare not process provider");
            }

            $this->configProviders[] = $provider;
        }
    }


    /**
     * @param string|ServiceProvider $provider
     */
    public function registerProcessService($provider): void
    {
        $provider = $this->parseProvider(
            $provider,
            $this->processContainer
        );

        if (isset($provider)) {
            if (
                $provider instanceof ServiceProvider
                && !$provider->isProcessServiceProvider()
            ) {
                $name = get_class($provider);
                $this->getConsoleLogger()->warning("register process provider $name which declare not process provider");
            }

            $this->processProviders[] = $provider;
        }
    }


    public function registerConversationService($providerName): void
    {
        $provider = $this->parseProvider(
            $providerName,
            $this->conversationContainer
        );

        if (isset($provider)) {
            if (
                $provider instanceof ServiceProvider
                && $provider->isProcessServiceProvider()
            ) {
                $name = get_class($provider);
                $this->getConsoleLogger()->warning("register conversation provider $name which declare process provider");
            }

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
                    ->warning("try to register worker process provider $providerName which already loaded");
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

        throw new BootingException(
            __METHOD__
            . ' only accept class name or instance of '
            . ServiceProvider::class
        );
    }

    public function bootApp() : Blueprint
    {
        // 不要重复启动.
        if ($this->workerBooted) {
            return $this;
        }

        $logger = $this->consoleLogger;

        try {

            $logger->info(static::class . ' start boot chatbot app');

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
                . get_class($this->processContainer)
            );

            $logger->info(static::class . ' booting chatbot app');

            /**
             * @var ServiceProvider[] $providers
             */
            $providers = array_merge($this->configProviders, $this->processProviders);

            // baseContainer 执行boot流程.
            foreach ($providers as $provider) {
                $logger->debug("boot provider " . get_class($provider));
                $provider->boot($this->processContainer);
            }

            $logger->info(static::class . ' chatbot app booted');
            $this->workerBooted = true;

            return $this;

        } catch (\Throwable $e) {
            $fatal =  new BootingException('fail to boot app', $e);
            $logger->emergency($fatal);
            throw $fatal;
        }
    }

    protected function baseBinding() : void
    {
        // 绑定默认组件到容器上.
        $this->consoleLogger->info("self binding....... ");

        // self
        $this->processContainer->instance(Blueprint::class, $this);
        $this->conversationContainer->instance(Blueprint::class, $this);

        // config
        $this->processContainer->instance(ChatbotConfig::class, $this->config);
        $this->conversationContainer->instance(ChatbotConfig::class, $this->config);

        $this->processContainer->instance(OOHostConfig::class, $this->config->host);
        $this->conversationContainer->instance(OOHostConfig::class, $this->config->host);

        // server
        $this->processContainer->instance(ConsoleLogger::class, $this->consoleLogger);
        $this->conversationContainer->instance(ConsoleLogger::class, $this->consoleLogger);

        // kernel
        $this->processContainer->singleton(ChatKernel::class, $this->chatKernel);

        // server
        $this->processContainer->singleton(ChatServer::class, $this->config->server);
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

    public function getProcessContainer() : ContainerContract
    {
        return $this->processContainer;
    }

    public function getConversationContainer() : ConversationContainer
    {
        return $this->conversationContainer;
    }

    public function getKernel(): ChatKernel
    {
        $this->bootApp();
        return $this->processContainer->make(ChatKernel::class);
    }

    public function getServer(): ChatServer
    {
        $this->bootApp();
        return $this->processContainer->make(ChatServer::class);
    }
}