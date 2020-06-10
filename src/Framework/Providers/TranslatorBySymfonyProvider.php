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
use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Yaml\YmlStorageOption;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $defaultLocale
 * @property-read string $defaultDomain
 * @property-read StorageOption|null $storage
 * @property-read StorageOption|null $initStorage
 */
class TranslatorBySymfonyProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'defaultLocale' => Translator::DEFAULT_LOCALE,
            'defaultDomain' => Translator::DEFAULT_DOMAIN,
            'storage' => null,
            'initStorage' => null
        ];
    }

    public static function relations(): array
    {
        return [
            'storage' => StorageOption::class,
            'initStorage' => StorageOption::class,
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

            return new SymfonyTranslatorAdapter(
                $registry->getCategory(TransOption::class),
                $logger,
                $this->defaultLocale,
                $this->defaultDomain
            );
        });
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var OptRegistry $registry
         */
        $registry = $app->get(OptRegistry::class);

        $storage = $this->storage;
        $initStorage = $this->initStorage;

        $storage = $storage
            ?? new YmlStorageOption([
                'path' => StringUtils::gluePath(
                    CommuneEnv::getResourcePath(),
                    'trans/lang.yml'
                ),
                'isDir' => false,
            ]);

        $registry->registerCategory(new CategoryOption([
            'name' => TransOption::class,
            'optionClass' => TransOption::class,
            'title' => '翻译模块',
            'desc' => '翻译模块的默认语料',
            'storage' => $storage->toMeta(),
            'initialStorage' => isset($initStorage) ? $initStorage->toMeta() : null,
        ]));
    }

}