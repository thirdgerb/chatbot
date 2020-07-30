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
use Commune\Framework\Trans\SymfonyTranslatorAdapter;
use Commune\Framework\Trans\TransOption;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Meta\StorageMeta;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Json\JsonStorageOption;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $defaultLocale
 * @property-read string $defaultDomain
 * @property-read StorageMeta|null $storage
 * @property-read StorageMeta|null $initStorage     定义自己的 init storage.
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

        if (CommuneEnv::isResetRegistry()) {
            $logger->warning("reset trans data!!");
            $category = $registry->getCategory(TransOption::class);
            $category->flush(false);
            $category->initialize();
        }

    }

    protected function getDefaultStorageMeta() : StorageMeta
    {
        return (new JsonStorageOption([
            'path' => StringUtils::gluePath(
                CommuneEnv::getResourcePath(),
                'trans/lang.json'
            ),
            'isDir' => false,
        ]))->toMeta();
    }

}