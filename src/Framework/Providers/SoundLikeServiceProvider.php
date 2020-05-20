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

use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Support\SoundLike\PinyinParser;
use Commune\Support\SoundLike\SoundLikeInterface;
use Commune\Support\SoundLike\SoundLikeManager;
use Overtrue\Pinyin\MemoryFileDictLoader;
use Overtrue\Pinyin\Pinyin;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string[] $parsers
 */
class SoundLikeServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'parsers' => [
                SoundLikeInterface::ZH =>  PinyinParser::class
            ],
        ];
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var SoundLikeInterface $soundLike
         */
        $soundLike = $app->get(SoundLikeInterface::class);

        foreach ($this->parsers as $lang => $parser) {
            $soundLike->register($lang, $parser);
        }
    }

    public function register(ContainerContract $app): void
    {
        if ($app->bound(SoundLikeInterface::class)) {
            return;
        }

        // 绑定 manager
        $app->singleton(
            SoundLikeInterface::class,
            function($app) {
                return new SoundLikeManager($app);
            }
        );

        // 注册拼音模块
        $app->singleton(
            PinyinParser::class,
            function() {
                $pinyin = new Pinyin(MemoryFileDictLoader::class);
                return new PinyinParser($pinyin);
            }
        );
    }


}