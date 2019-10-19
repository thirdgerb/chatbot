<?php


namespace Commune\Chatbot\Framework\Component;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Bootstrap\Bootstrapper;
use Commune\Chatbot\Framework\Bootstrap\LoadComponents;
use Commune\Chatbot\Framework\Component\Providers;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\HostProcessServiceProvider;
use Commune\Chatbot\OOHost\NLU\Contracts\CorpusOption;
use Commune\Support\Option;
use Commune\Support\OptionRepo\Options\CategoryMeta;

/**
 * 组件的功能:
 * 0. 依赖其它组件.
 * 1. 提供服务注册.  service provider
 * 2. 提供一套配置
 * 3. 提供一些默认的功能.
 * - self register 模块
 * - translation 模块
 * - feeling模块
 *
 */
abstract class ComponentOption extends Option implements Bootstrapper
{
    /**
     * @var Application
     */
    protected $app;

    public function bootstrap(Application $app): void
    {
        $this->app = $app;
        $this->doBootstrap();
    }

    abstract protected function doBootstrap() : void;


    /*------ method for do bootstrap ------*/

    /**
     * 注册一个配置仓库.
     *
     * @param CategoryMeta $meta
     */
    public function loadOptionRepoCategoryMeta(
        CategoryMeta $meta
    ) : void
    {
        $this->app->registerConfigService(
            new Providers\LoadOptionRepoCategoryMeta(
                $this->app->getProcessContainer(),
                $meta
            )
        );

    }

    /**
     * 启动的时候预加载 SelfRegister 类
     * 这样做到基于配置文件可以预加载 intent, memory, context 等.
     *
     * scan and load self registering classes.
     * find class by psr-4
     *
     * @param string $namespace
     * @param string $path
     */
    public function loadSelfRegisterByPsr4(
        string $namespace,
        string $path
    ) : void
    {
        $this->app->registerProcessService(
            new Providers\LoadPsr4SelfRegister(
                $this->app->getProcessContainer(),
                $this->app->getConsoleLogger(),
                $namespace,
                $path
            )
        );
    }

    /**
     * 注册 replyId 的专属 render
     *
     * @param array $renders
     * @param bool $force
     */
    public function registerReplyRender(
        array $renders,
        bool $force = true
    ) : void
    {
        $this->app->registerProcessService(
            new Providers\LoadReplyRenders(
                $this->app->getProcessContainer(),
                $renders,
                $force
            )
        );
    }

    /**
     * 注册会话级的事件.
     * @param array $eventToListeners
     */
    public function registerConversationalEvents(
        array $eventToListeners
    ) : void
    {
        $this->app->registerProcessService(
            new Providers\LoadConversationalEvents(
                $this->app->getProcessContainer(),
                $eventToListeners
            )
        );
    }


    /**
     * 像 hostConfig 那样定义默认的 memory
     * @see HostProcessServiceProvider
     * @param array $memoryOptions
     */
    public function registerMemoryBag(
        array $memoryOptions
    ) : void
    {
        $this->app->registerProcessService(
            new Providers\LoadMemoryBag(
                $this->app->getProcessContainer(),
                $memoryOptions
            )
        );

    }

    /**
     * 添加翻译文件的资源.
     *
     * trans 文件夹的结构是 {$resourcePath}/语言名/{domain}.{format}
     * 例如  __DIR__/trans/zh/messages.php
     *
     * load translation resource.
     *
     * @param string $resourcePath
     * @param string $loader
     */
    public function loadTranslationResource(
        string $resourcePath,
        string $loader = Translator::FORMAT_PHP
    ) : void
    {
        $this->app->registerProcessService(
            new Providers\LoadTranslationConfig(
                $this->app->getProcessContainer(),
                $resourcePath,
                $loader
            )
        );
    }


    /**
     * 从yaml 文件中读取 option 配置,
     * 并保存到 optionRepository 仓库中.
     *
     * @param string $resourcePath
     * @param string $category
     * @param string $optionClazz
     * @param bool $force  如果 false, 只有目标 option 不存在, 才加载.
     */
    public function registerOptionFromYaml(
        string $resourcePath,
        string $category,
        string $optionClazz,
        bool $force = false
    ) : void
    {
        $this->app->registerConfigService(
            new Providers\RegisterOptionFromYaml(
                $this->app->getProcessContainer(),
                $resourcePath,
                $category,
                $optionClazz,
                $force
            )
        );
    }

    /**
     * 将 yaml 文件读取的所有 option 与 corpus 语料库同步.
     *
     * @param string $resourcePath  : yaml 的路径
     * @param string $corpusOptionClazz     : corpus option 的类名
     * @param bool $force   : force 为 false 时, 只有语料库不存在该 option, 才同步.
     * @param bool $sync    : 决定 corpus 会不会把 option 存储到 option repository 中.
     */
    public function registerCorpusOptionFromYaml(
        string $resourcePath,
        string $corpusOptionClazz,
        bool $force = false,
        bool $sync = false
    ) : void
    {
        if ( ! is_a($corpusOptionClazz, CorpusOption::class, TRUE)) {
            throw new ConfigureException(
                __METHOD__
                . ' only accept option class implements '
                . CorpusOption::class
            );
        }

        $this->app->registerProcessService(
            new Providers\RegisterCorpusOptionFromYaml(
                $this->app->getProcessContainer(),
                $resourcePath,
                $corpusOptionClazz,
                $force,
                $sync
            )
        );
    }

    /**
     * 增加情感的映射
     * @param string $emotionName
     * @param string|callable $experience
     */
    public function addFeelingExperience(
        string $emotionName,
        $experience
    ) : void
    {
        if (!isset($this->loadEmotions)) {

            $loadEmotions = new Providers\LoadEmotions($this->app->getProcessContainer());
            $this->loadEmotions = $loadEmotions;
            $this->app->registerProcessService($loadEmotions);
        }
        $this->loadEmotions->addExperience($emotionName, $experience);
    }

    /**
     * @var Providers\LoadEmotions
     */
    protected $loadEmotions;



    /**
     * 依赖别的组件. 如果该组件已经注册, 则不会改动
     * 否则会按当前的配置加载一个该组件.
     *
     * depend on another component
     *
     * after load all components,
     * if the depending component is still not registered
     * then load the component by it's default option
     * ( todo or should throw exception? )
     *
     * @param string $componentName
     * @param array $data
     */
    public function dependComponent(
        string $componentName,
        array $data = []
    ) : void
    {
        LoadComponents::dependComponent(
            static::class,
            $componentName,
            $data
        );
    }
}