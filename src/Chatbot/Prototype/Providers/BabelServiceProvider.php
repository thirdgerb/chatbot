<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Prototype\Providers;

use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Support\Babel\Babel;
use Commune\Support\Babel\BabelResolver;
use Commune\Support\Babel\BabelSerializable;
use Commune\Message\Prototype;
use Commune\Support\Babel\JsonResolver;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string[] $serializable        可以序列化成字符串的类的类名.
 * @see BabelSerializable
 */
class BabelServiceProvider extends ServiceProvider
{

    public static function stub(): array
    {
        return [
            'serializable' => [
                Prototype\Convo\IText::class,
                Prototype\Convo\IUnsupported::class,
                Prototype\Convo\IJson::class,
                Prototype\Convo\IIntent::class,
                Prototype\Convo\Media\IAudio::class,
                Prototype\Convo\Media\IImage::class,
                Prototype\Convo\Media\ILink::class,
            ]
        ];
    }

    public function isProcessServiceProvider(): bool
    {
        return true;
    }

    public function boot(ContainerContract $app): void
    {
        $resolver = $app->get(BabelResolver::class);
        Babel::setResolver($resolver);

        /**
         * @var ConsoleLogger $console
         */
        $console = $app->get(ConsoleLogger::class);

        foreach ($this->serializable as $className) {
            $console->debug("babel register $className");
            $resolver->registerSerializable($className);
        }
    }

    public function register(ContainerContract $app): void
    {
        $app->bind(BabelResolver::class, JsonResolver::class);
    }



}