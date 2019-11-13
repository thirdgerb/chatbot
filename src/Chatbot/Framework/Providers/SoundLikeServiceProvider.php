<?php

/**
 * Class SoundLikeServiceProvider
 * @package Commune\Chatbot\Framework\Providers
 */

namespace Commune\Chatbot\Framework\Providers;

use Commune\Support\SoundLike\PinyinParser;
use Commune\Support\SoundLike\SoundLikeInterface;
use Commune\Support\SoundLike\SoundLikeManager;
use Overtrue\Pinyin\MemoryFileDictLoader;
use Overtrue\Pinyin\Pinyin;

/**
 * Sound like 的服务注册
 * 可以自己写一个 process service provider, 注册自己想要的 parser
 */
class SoundLikeServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    public function boot($app)
    {
    }

    public function register()
    {
        if ($this->app->bound(SoundLikeInterface::class)) {
            return;
        }

        $this->app->singleton(SoundLikeInterface::class, function(){

            $pinyin = new Pinyin(MemoryFileDictLoader::class);
            $zhParser = new PinyinParser($pinyin);

            $manager = new SoundLikeManager();
            $manager->registerParser(SoundLikeInterface::ZH, $zhParser);
            return $manager;
        });
    }


}