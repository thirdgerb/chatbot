<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Component\Providers;

use Commune\Blueprint\CommuneEnv;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\ServiceProvider;
use Commune\Contracts\Trans\Translator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $path
 * @property-read bool $intl
 * @property-read bool $force
 *
 */
class LoadTranslations extends ServiceProvider
{

    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => '',
            'path' =>  '',
            'intl' => true,
            'force' => false,
        ];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public function boot(ContainerContract $app): void
    {
        $translator = $app->get(Translator::class);
        $logger = $app->get(ConsoleLogger::class);

        static::load(
            $this->path,
            $translator,
            $logger,
            $this->intl,
            $this->force || CommuneEnv::isResetRegistry()
        );
    }

    public static function load(
        string $path,
        Translator $translator,
        LoggerInterface $logger,
        bool $intl,
        bool $force
    )
    {
        // 遍历翻译文件所在目录.
        $dirFinder = new Finder();
        $generator = $dirFinder->directories()
            ->in($path)
            ->depth(0);

        foreach($generator as $dir) {
            /**
             *
             * @var \SplFileInfo $dir
             */
            $locale = $dir->getBasename();
            // 认为文件夹名字就是地域命名.

            // 遍历该文件夹下所有文件, 不递归查找.
            $fileFinder = new Finder();
            foreach(
                $fileFinder->files()
                    ->in($dir->getPathname())
                    ->depth(0)
                    ->name('*.php')
                as $file
            ) {
                // 认为文件名就是domain
                /**
                 * @var \SplFileInfo $file
                 */
                $domain = str_replace('.'.$file->getExtension(), '', $file->getBasename());
                $path = $file->getPathname();

                // 记录日志
                $logger->debug("register trans resource, locale: $locale, domain:$domain, path:$path");

                $data = include $path;

                $translator->saveMessages(
                    $data,
                    $locale,
                    $domain,
                    $intl,
                    $force
                );
            }
        }
    }

    public function register(ContainerContract $app): void
    {
    }


}