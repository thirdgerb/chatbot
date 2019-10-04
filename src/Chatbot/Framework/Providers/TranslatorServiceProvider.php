<?php

/**
 * Class TranslatorServiceProvider
 * @package Commune\Chatbot\Framework\Providers
 */

namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\Translator;
use Symfony\Component\Translation\Loader\CsvFileLoader;
use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Commune\Chatbot\Framework\Predefined\SymfonyTranslatorAdapter;

/**
 * 翻译组件的服务注册.
 * 从文件夹里读取基础配置.
 * 用于 chatbot 的回复, 以及 validator 等组件
 * 默认的 domain 是 messages
 *
 * Class TranslatorServiceProvider
 * @package Commune\Chatbot\Framework\Providers
 */
class TranslatorServiceProvider extends BaseServiceProvider
{
    use TranslationLoader;

    public const IS_PROCESS_SERVICE_PROVIDER = true;

    public function boot($app): void
    {
        $this->loading($app);
    }

    public function register(): void
    {
        if ($this->app->bound(Translator::class)) {
            return;
        }


        // 注册symfony 的组件.
        $this->app->singleton(SymfonyTranslator::class, function(\ArrayAccess $app){
            /**
             * @var ChatbotConfig $chatbotConfig
             */
            $chatbotConfig = $app[ChatbotConfig::class];
            $config = $chatbotConfig->translation;

            $translator = new SymfonyTranslator(
                $config->defaultLocale,
                null,
                $config->cacheDir,
                CHATBOT_DEBUG
            );

            // 定义fileLoader.
            $translator->addLoader(Translator::FORMAT_YAML, new YamlFileLoader());
            $translator->addLoader(Translator::FORMAT_JSON, new JsonFileLoader());
            $translator->addLoader(Translator::FORMAT_CSV, new CsvFileLoader());
            $translator->addLoader(Translator::FORMAT_XLIFF, new XliffFileLoader());
            $translator->addLoader(Translator::FORMAT_PHP, new PhpFileLoader());

            return $translator;
        });

        // alias
        $this->app->alias(SymfonyTranslator::class, TranslatorInterface::class);

        $this->app->singleton(Translator::class, function($app){
            /**
             * @var SymfonyTranslator $symfony
             * @var ChatbotConfig $config
             */
            $symfony = $app[SymfonyTranslator::class];
            $config = $app[ChatbotConfig::class];

            return new SymfonyTranslatorAdapter($symfony, $config->translation);
        });

    }


}