<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Providers;

use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Framework\Prototype\Intercom\IShellInput;
use Commune\Support\Babel\Babel;
use Commune\Support\Babel\BabelResolver;
use Commune\Support\Babel\BabelSerializable;
use Commune\Message\Predefined;
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

                // message
                Predefined\IText::class,
                Predefined\IUnsupported::class,
                Predefined\IJson::class,
                Predefined\IIntentMsg::class,
                Predefined\Media\IAudio::class,
                Predefined\Media\IImage::class,
                Predefined\Media\ILink::class,

                // intercom
                IShellInput::class,
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