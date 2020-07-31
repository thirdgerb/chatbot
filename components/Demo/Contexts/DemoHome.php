<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo\Contexts;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Components\Demo\Git\GitContext;
use Commune\Components\Demo\Maze\PlayMaze;
use Commune\Ghost\Context\ACodeContext;
use Commune\Support\Registry\OptRegistry;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @desc Demo的入口
 */
class DemoHome extends ACodeContext
{
    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'onQuit' => 'quit',
            'onCancel' => 'cancel',
        ]);
    }

    public function __on_start(Stage $stage): Stage
    {
        return $stage
            ->onActivate(function(Activate $dialog){
                return $dialog->next('menu');
            });
    }


    public function __on_quit(Stage $stage) : Stage
    {
        return $stage->always(function(Dialog $dialog){
            return $dialog
                ->send()
                ->notice('quiting pass by quit stage')
                ->over()
                ->quit();
        });
    }

    public function __on_cancel(Stage $stage) : Stage
    {
        return $stage->always(function(Dialog $dialog){
            return $dialog
                ->send()
                ->notice('canceling pass by cancel stage')
                ->over()
                ->quit();
        });
    }

    public function __on_menu(Stage $stage) : Stage
    {
        /**
         * @var OptRegistry $a
         */
        $ab = $stage->dialog->cloner->mind;
        $ab->stageReg()->hasDef(static::__name());

        return $stage
            ->onActivate(function(Activate $dialog){

                return $dialog
                    ->await()
                    ->askChoose(
                        '请您选择',
                        [
                            FeatureTest::genUcl(),
                            PlayMaze::genUcl(),
                            GitContext::genUcl(),
                        ]
                    );

            })
            ->onResume(function(Dialog $dialog) {
                $dialog->send()
                    ->info('完成测试')
                    ->over()
                    ->quit();
            });

    }


}