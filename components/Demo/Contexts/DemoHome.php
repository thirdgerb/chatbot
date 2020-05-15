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

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Host\Contexts\ACodeContext;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DemoHome extends ACodeContext
{
    const DESCRIPTION = "demo的入口";

    public function __on_start(Stage $stage): StageDef
    {
        return $stage
            ->onActivate(function(Activate $dialog){
               return $dialog->nav()->next('menu');
            })
            ->onEvent(Dialog::QUIT, function(Dialog $dialog) {
                $dialog->send()->info('quit from quiting event');
                return null;
            })
            ->onEvent(Dialog::CANCEL, function(Dialog $dialog) {
                $dialog->send()->info('quit from cancel event');
                return null;
            })
            ->end();
    }

    public function __on_menu(Stage $stage) : StageDef
    {
        return $stage
            ->onActivate(function(Activate $dialog){

                return $dialog
                    ->await()
                    ->askChoose(
                        '请您选择',
                        [
                            FeatureTest::class,
                            WelcomeUser::class,
                            DevTools::class,
                        ]
                    );

            })
            ->onEvent(
                Dialog::FALLBACK,
                function(Dialog $dialog) {
                    $dialog->send()
                        ->info('完成测试')
                        ->over();

                    return $dialog->nav()->quit();
                }
            )
            ->end();

    }
}