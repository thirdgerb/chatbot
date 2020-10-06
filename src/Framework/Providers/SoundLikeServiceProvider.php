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
 * 基于 PinyinParser 实现的中文拼音模块.
 * 许多对话系统拼音转文字并不准确, 而在具体场景中往往只要发音相似就够了.
 * 因此粗糙地实现了一个语音相似匹配的模块, 可作为语音转文字模块的补充.
 *
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

    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
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
        if ($app->has(SoundLikeInterface::class)) {
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