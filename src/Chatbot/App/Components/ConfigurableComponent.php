<?php


namespace Commune\Chatbot\App\Components;


use Commune\Chatbot\App\Components\Configurable\Providers\AbsConfigurableServiceProvider;
use Commune\Chatbot\App\Components\Configurable\Providers\JsonLoaderServiceProvider;
use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;

/**
 * 可配置的整个 context
 * 尚未完工.
 *
 * @property-read string $serviceProvider
 * @property-read string $defaultPath
 * @property-read string[] $resources
 * @property-read string $trans
 */
class ConfigurableComponent extends ComponentOption
{


    public static function stub(): array
    {
        return [
            'serviceProvider' => JsonLoaderServiceProvider::class,
            'defaultPath' => __DIR__ . '/Configurable/cache',
            'resources' => [
            ],
            'trans' => __DIR__ . '/Configurable/trans',
        ];
    }

    protected function doBootstrap(): void
    {
        $this->registerComponentServiceProvider();

        // 先加载当前组件内的模块.
        $this->loadSelfRegisterByPsr4(
            'Commune\\Chatbot\\App\\Components\\Configurable\\',
            __DIR__ .'/Configurable'
        );

        // 加载必要的翻译文件.
        if (!empty($this->trans)) {
            $this->loadTranslationResource(__DIR__.'/Configurable/trans');
        }
    }

    protected function registerComponentServiceProvider() : void
    {
        $name = $this->serviceProvider;
        if (!is_a($name, AbsConfigurableServiceProvider::class, TRUE)) {
            throw new ConfigureException(
                static::class
                . ' service provider should be instance of '
                .  AbsConfigurableServiceProvider::class
            );
        }

        $provider = new $name(
            $this->app->getProcessContainer(),
            $this
        );

        $this->app->registerProcessService($provider);
    }



}