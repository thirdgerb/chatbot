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
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Components\Predefined\Memory\UserInfoMem;
use Commune\Host\Contexts\CodeContext\DefineParam;
use Commune\Host\Contexts\CodeContext\HasEntity;
use Commune\Blueprint\Ghost\Context\ParamBuilder;
use Commune\Host\Contexts\ACodeContext;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;

/**
 * 正式的欢迎用户入口.
 *
 * @property UserInfoMem $mem
 *
 * 依赖一个用户的上下文记忆, 自动开启多轮对话进行信息填充.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class WelcomeUser extends ACodeContext implements
    DefineParam,
    HasEntity
{


    public static function __params(ParamBuilder $param) : ParamBuilder
    {
        return $param->dependOn('mem', UserInfoMem::class);
    }

    public static function __entities(): array
    {
        return ['mem'];
    }

    /**
     * 欢迎语
     *
     * @param Stage $stage
     * @return StageDef
     */
    public function __on_start(Stage $stage): StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog){

                $this->mem->increaseLoginTimes();

                $dialog
                    ->send()
                    ->info(
                        'demo.dialog.welcomeUser',
                        [
                            'name' => $this->mem->name,
                            'count' => $this->mem->loginTimes
                        ]
                    );

                return $dialog->redirect()->next('menu');

            })
            ->end();
    }

    /**
     * 菜单界面
     * @param Stage $stage
     * @return StageDef
     */
    public function __on_menu(Stage $stage) : StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog) : Dialog {
                return $dialog
                    ->redirect()
                    ->await(
                        'ask.needs',
                        [
                            'sw.demo.intro',
                            GameTestCases::class,
                            NLTestCases::class,
                            '命令行工具' => [$this, 'showScript'],
                            '退出' => Redirector::goQuit(),
                        ]
                    );
            })
            ->onEvent(
                Dialog::HEED,
                function(Dialog $dialog) {

                    return $dialog
                        ->hearing()
                        ->todo([$this, 'showScript'])
                            ->isChoice(3)
                        ->todo($dialog->redirect()->quit())
                            ->isChoice(4)
                        ->end();
                }
            )
            ->end();
    }

    public function showScript(Dialog $dialog) : Dialog
    {
        $dialog->send()
            ->info('demo.dialog.commandTest')
            ->info('demo.dialog.helpInfo');

        return $dialog->redirect()->reactivate();
    }




}