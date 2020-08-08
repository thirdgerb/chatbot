<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Providers;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\MindSelfRegister;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\ServiceProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string[] $psr4
 */
class Psr4SelfRegisterLoader extends ServiceProvider
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => static::class,
            'psr4' => [],
        ];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public function boot(ContainerContract $app): void
    {
        $mind = $app->get(Ghost\Mindset::class);
        $logger = $app->get(ConsoleLogger::class);

        foreach ($this->psr4 as $namespace => $path) {
            static::loadSelfRegister(
                $mind,
                $namespace,
                $path,
                $logger
            );
        }
    }

    public function register(ContainerContract $app): void
    {
    }


    public static function loadSelfRegister(
        Ghost\Mindset $mind,
        string $namespace,
        string $directory,
        LoggerInterface $logger
    ) : void
    {
        $directory = realpath($directory);
        if ($directory === false) {
            throw new CommuneLogicException("directory $directory not exists");
        }

        $finder = new Finder();
        $finder->files()
            ->in($directory)
            ->name('/\.php$/');

        $i = 0;
        foreach ($finder as $fileInfo) {

            $path = $fileInfo->getPathname();
            $name = str_replace($directory, '', $path);
            $name = str_replace('.php', '', $name);
            $name = str_replace('/', '\\', $name);

            $clazz = trim($namespace, '\\')
                . '\\' .
                trim($name, '\\');

            if (!is_a($clazz, MindSelfRegister::class, TRUE)) {
                continue;
            }

            // 判断是不是可以实例化的.
            $r = new \ReflectionClass($clazz);

            if ($r->isAbstract() || $r->isInterface()) {
                continue;
            }

            $logger->debug("load mind self register: $clazz");
            $method = [$clazz, MindSelfRegister::REGISTER_METHOD];
            $reset = CommuneEnv::isResetRegistry();
            call_user_func($method, $mind, $reset);
            $i ++;
        }

        if (empty($i)) {
            $logger->warning(
                'no self register class found,'
                . "namespace is $namespace,"
                . "directory is $directory"
            );
        }

    }

}