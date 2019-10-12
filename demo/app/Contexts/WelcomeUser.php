<?php


namespace Commune\Demo\App\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Predefined\Memories\UserInfoMem;

/**
 * 正式的欢迎用户入口.
 *
 * @property UserInfoMem $mem
 */
class WelcomeUser extends TaskDef
{
    const DESCRIPTION = 'demo.contexts.welcomeUser';

    public static function __depend(Depending $depending): void
    {
        $depending->onMemory('mem', UserInfoMem::class);
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
        return $stage->buildTalk()->action(Redirector::goHome());

    }

    public function __exiting(Exiting $listener): void
    {
    }


}