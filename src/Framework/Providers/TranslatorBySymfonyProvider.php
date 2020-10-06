<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Providers;

use Commune\Blueprint\CommuneEnv;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\ServiceProvider;
use Commune\Contracts\Trans\Translator;
use Commune\Framework\Component\Providers\LoadTranslations;
use Commune\Framework\Trans\SymfonyTranslatorAdapter;
use Commune\Framework\Trans\TransOption;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Meta\StorageMeta;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Json\JsonStorageOption;
use Commune\Support\Utils\StringUtils;

/**
 * 基于 Symfony 实现的 translator 模块
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $defaultLocale             系统默认的 locale
 * @property-read string $defaultDomain             系统默认的 domain
 * @property-read StorageMeta|null $storage         自定义的 storage
 * @property-read StorageMeta|null $initStorage     自定义的 init storage. 默认是文件.
 *
 *
 * @property-read string $resource                  初始资源所在文件夹. 内部继续按 zh-cn/messages.php 这样的方式来定义 locale 与 domain.
 *
 *
 * @property-read bool $load                        是否加载资源.
 * @property-read bool $reset                       是否重置所有的资源.
 */
class TranslatorBySymfonyProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'defaultLocale' => Translator::DEFAULT_LOCALE,
            // domain 可以设置为 shell 的名称.
            'defaultDomain' => Translator::DEFAULT_DOMAIN,

            'storage' => null,

            'initStorage' => null,

            'resource' => StringUtils::gluePath(
                CommuneEnv::getResourcePath(),
                'trans'
            ),


            'load' => CommuneEnv::isLoadingResource(),
            'reset' => CommuneEnv::isResetRegistry(),
        ];
    }

    public static function relations(): array
    {
        return [
            'storage' => StorageMeta::class,
            'initStorage' => StorageMeta::class,
        ];
    }

    public function getId(): string
    {
        return Translator::class;
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_CONFIG;
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(Translator::class, function (ContainerContract $app) {

            /**
             * @var OptRegistry $registry
             */
            $registry = $app->get(OptRegistry::class);
            $logger = $app->get(ConsoleLogger::class);

            $category = $registry->getCategory(TransOption::class);
            
            $translator = new SymfonyTranslatorAdapter(
                $category,
                $logger,
                $this->defaultLocale,
                $this->defaultDomain
            );
            
            return $translator;
        });
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var OptRegistry $registry
         */
        $registry = $app->get(OptRegistry::class);

        $storage = $this->storage;
        $initStorage = $this->initStorage ?? $this->getDefaultStorageMeta();

        $logger = $app->get(ConsoleLogger::class);


        $registry->registerCategory(new CategoryOption([
            'name' => TransOption::class,
            'optionClass' => TransOption::class,
            'title' => '翻译模块',
            'desc' => '翻译模块的默认语料',
            'storage' => $storage,
            'initialStorage' => $initStorage,
        ]));

        // 如果要重置, 清空所有的数据.
        if ($this->reset) {
            $logger->warning("reset trans data!!");
            $category = $registry->getCategory(TransOption::class);
            $category->flush(false);
            $category->initialize();
        }

        // 看看是否要加载资源.
        if (!$this->load) {
            return;
        }

        // 加载最初的配置.
        LoadTranslations::load(
            $this->resource,
            $app->make(Translator::class),
            $logger,
            true,
            true
        );

    }

    protected function getDefaultStorageMeta() : StorageMeta
    {
        return (new JsonStorageOption([
            'path' => StringUtils::gluePath(
                CommuneEnv::getRuntimePath(),
                'trans/lang.json'
            ),
            'isDir' => false,
        ]))->toMeta();
    }

}