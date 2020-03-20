<?php


namespace Commune\Components\Demo\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Predefined\Memories\UserInfoMem;

/**
 * 正式的欢迎用户入口.
 *
 * @property UserInfoMem $mem
 *
 * 依赖一个用户的上下文记忆, 自动开启多轮对话进行信息填充.
 *
 */
class WelcomeUser extends TaskDef
{
    const DESCRIPTION = 'demo.contexts.welcomeUser';

    public static function __depend(Depending $depending): void
    {
        $depending->onMemory('mem', UserInfoMem::class);
    }

    public function __exiting(Exiting $listener): void
    {
    }

    public function __hearing(Hearing $hearing) : void
    {
        $hearing->runAnyIntent();
    }

    /**
     * 欢迎语
     * @param Stage $stage
     * @return Navigator
     */
    public function __onStart(Stage $stage): Navigator
    {
        $this->mem->increaseLoginTimes();

        return $stage->buildTalk()
            ->info(
                'demo.dialog.welcomeUser',
                [
                    'name' => $this->mem->name,
                    'count' => $this->mem->loginTimes
                ]
            )->goStage('menu');
    }

    /**
     * 菜单界面
     * @param Stage $stage
     * @return Navigator
     */
    public function __onMenu(Stage $stage) : Navigator
    {
        $menu = new Menu(
            'ask.needs',
            [
                'sw.demo.intro',
                GameTestCases::class,
                NLTestCases::class,
                '命令行工具' => [$this, 'showScript'],
                '退出' => Redirector::goQuit(),
            ]
        );
        return $stage->component($menu);

    }


    public function showScript(Dialog $dialog) : Navigator
    {
        $dialog->say()
            ->info('demo.dialog.commandTest')
            ->info('demo.dialog.helpInfo');
        return $dialog->repeat();
    }


}