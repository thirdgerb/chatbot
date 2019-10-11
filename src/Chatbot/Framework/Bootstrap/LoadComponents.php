<?php


namespace Commune\Chatbot\Framework\Bootstrap;


use Commune\Chatbot\OOHost\NLU\NLUComponent;
use Commune\Components\Predefined\PredefinedComponent;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Psr\Log\LoggerInterface;

class LoadComponents implements Bootstrapper
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Application
     */
    protected $app;

    protected static $registerLater = [];

    public function bootstrap(Application $app): void
    {
        $config = $app->getConfig();
        $logger = $app->getConsoleLogger();


        // 系统默认的意图是预加载的.
        static::dependComponent('system-predefined', PredefinedComponent::class);

        // 预加载默认的 nlu 管理.
        static::dependComponent('system-nlu', NLUComponent::class);

        // 遍历系统注册的components, 一一注册.
        foreach ($config->components as $index => $name) {

            if (is_string($index) && is_array($name)) {
                $this->registerComponent($app, $logger, $index, $name);

            } elseif (is_int($index) && is_string($name)) {
                $this->registerComponent($app, $logger, $name);

            } else {
                $this->logger->warning(
                    "invalid component registration, key $index, "
                    . var_export($name, true)
                );
            }
        }

        $processIoc = $app->getProcessContainer();

        foreach (self::$registerLater as $dependency => list ($name, $data)) {

            if ($processIoc->has($name)) {
                $logger->debug("component $name depended by $dependency has been register");
                return;
            }
            $this->registerComponent($app, $logger, $name, $data);
        }
    }

    /**
     * 标记依赖一个component, 如果在chatbotConfig 里没有注册这个Component, 会最后加载.
     *
     * @param string $dependBy
     * @param string $componentName
     * @param array $data
     */
    public static function dependComponent(
        string $dependBy,
        string $componentName,
        array $data = []
    ) : void
    {
        self::$registerLater[$dependBy] =  [$componentName, $data];
    }

    /**
     * 执行逻辑注册一个component
     *
     * @param Application $app
     * @param LoggerInterface $logger
     * @param string $clazz
     * @param array $data
     */
    public static function registerComponent(
        Application $app,
        LoggerInterface $logger,
        string $clazz,
        array $data = []
    ) : void
    {
        if (!is_a($clazz, ComponentOption::class, TRUE)) {
            throw new ConfigureException("invalid component class $clazz");
        }

        $logger->debug("registering component $clazz");
        /**
         * @var ComponentOption $bootstrapper
         */
        $bootstrapper = new $clazz($data);

        $processIoc = $app->getProcessContainer();


        $processIoc->instance($clazz, $bootstrapper);
        $bootstrapper->bootstrap($app);
    }

}