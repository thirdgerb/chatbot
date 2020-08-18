<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\Jieba;

use Commune\Blueprint\NLU\NLUManager;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read NLUServiceOption $option
 * @property-read JiebaOption $jiebaOption
 */
class JiebaServiceProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public static function stub(): array
    {
        return [
            'option' => JiebaTokenizer::defaultOption(),
            'jiebaOption' => [

            ],
        ];
    }

    public static function relations(): array
    {
        return [
            'jiebaOption' => JiebaOption::class,
        ];
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var NLUManager $manager
         */
        $manager = $app->make(NLUManager::class);
        $manager->registerService(
            $this->option ?? JiebaTokenizer::defaultOption()
        );

        // 初始化 jieba. 注意需要 600mb 左右的内存空间.
        $app->make(JiebaTokenizer::class);
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(JiebaTokenizer::class, function(){
            return new JiebaTokenizer(
                $this->option ?? JiebaTokenizer::defaultOption(),
                $this->jiebaOption
            );
        });
    }


}