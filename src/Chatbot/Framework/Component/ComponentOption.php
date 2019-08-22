<?php


namespace Commune\Chatbot\Framework\Component;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Bootstrap\Bootstrapper;
use Commune\Chatbot\Framework\Bootstrap\LoadComponents;
use Commune\Chatbot\Framework\Component\Providers\LoadEmotions;
use Commune\Chatbot\Framework\Component\Providers\LoadNLUExamplesFromJson;
use Commune\Chatbot\Framework\Component\Providers\LoadPsr4SelfRegister;
use Commune\Chatbot\Framework\Component\Providers\LoadTranslationConfig;
use Commune\Support\Option;

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
            new LoadPsr4SelfRegister(
                $this->app->getProcessContainer(),
                $this->app->getConsoleLogger(),
                $namespace,
                $path
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
            new LoadTranslationConfig(
                $this->app->getProcessContainer(),
                $resourcePath,
                $loader
            )
        );
    }


    /**
     * @param string $resourcePath
     * @deprecated
     */
    public function loadNLUExampleFromJsonFile(
        string $resourcePath
    ) : void
    {
        $this->app->registerProcessService(
            new LoadNLUExamplesFromJson(
                $this->app->getProcessContainer(),
                $resourcePath
            )
        );
    }


    /**
     * @var LoadEmotions
     */
    protected $loadEmotions;

    /**
     * @param string $emotionName
     * @param string|callable $experience
     */
    public function addFeelingExperience(
        string $emotionName,
        $experience
    ) : void
    {
        if (!isset($this->loadEmotions)) {

            $loadEmotions = new LoadEmotions($this->app->getProcessContainer());
            $this->loadEmotions = $loadEmotions;
            $this->app->registerProcessService($loadEmotions);
        }
        $this->loadEmotions->addExperience($emotionName, $experience);
    }


    /**
     * 注册别的组件.
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