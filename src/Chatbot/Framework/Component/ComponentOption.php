<?php


namespace Commune\Chatbot\Framework\Component;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Bootstrap\Bootstrapper;
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

    /**
     * 启动的时候预加载 SelfRegister 类
     * 这样做到基于配置文件可以预加载 intent, memory, context 等.
     *
     * @param string $namespace
     * @param string $path
     */
    public function selfRegisterByPsr4(
        string $namespace,
        string $path
    ) : void
    {
        $this->app->registerReactorService(
            new LoadPsr4SelfRegister(
                $this->app->getReactorContainer(),
                $this->app->getReactorLogger(),
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
     * @param string $resourcePath
     * @param string $loader
     */
    public function addTranslationResource(
        string $resourcePath,
        string $loader = Translator::FORMAT_PHP
    ) : void
    {
        $this->app->registerReactorService(
            new LoadTranslationConfig(
                $this->app->getReactorContainer(),
                $resourcePath,
                $loader
            )
        );
    }

}