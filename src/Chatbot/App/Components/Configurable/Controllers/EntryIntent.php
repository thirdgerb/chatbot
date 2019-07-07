<?php


namespace Commune\Chatbot\App\Components\Configurable\Controllers;


use Commune\Chatbot\App\Callables\Intercepers\MustBeSupervisor;
use Commune\Chatbot\App\Components\Configurable\Controllers;
use Commune\Chatbot\App\Intents\ActionIntent;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @property string $redirect
 */
class EntryIntent extends ActionIntent
{
    const SIGNATURE = 'configurable:entry';

    const DESCRIPTION = '可配置模块的管理入口';

    public static function __depend(Depending $depending): void
    {
    }

    public function __hearing(Hearing $hearing)
    {
        $hearing->component(new Controllers\Operate());
    }

    protected static function getContextName(): string
    {
        return 'configurable.entry';
    }

    public function action(Stage $stage): Navigator
    {
        return $stage
            ->onStart(new MustBeSupervisor())
            ->buildTalk()
                ->info('开始管理对话可配置模块')
                ->goStage('menu');
    }

    public function __onMenu(Stage $stage) : Navigator
    {
        $operations = implode('|', Operate::OPERATIONS);
        $menu = new Menu(
            '您的操作是?',
            [
                Controllers\EditDomainController::class,
                Controllers\AllDomainController::class,
            ],
            function(
                string $context,
                Dialog $dialog,
                int $index
            ){
                $this->redirect = $context;
                return $dialog->goStage('redirect');
            },
            function(Dialog $dialog) {
                $dialog->say()->error("无法理解的操作");
                return $dialog->goStage('continue');
            },
            function(Hearing $hearing) {
                $hearing->isInstanceOf(
                    Controller::class,
                    function(Dialog $dialog) {
                        return $dialog->goStage('continue');
                    }
                );
            }
        );

        return $stage->buildTalk()
            ->info("随时可执行($operations)")
            ->toStage()
            ->component($menu);
    }

    public function __onRedirect(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->dependOn($this->redirect)
                ->info('回到管理入口')
                ->restart();
    }

    public function __onContinue(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askConfirm(
                '还需要继续吗?',
                true
            )->wait()
            ->hearing()
                ->isChoice(0, function(Dialog $dialog){
                    return $dialog->fulfill();

                })->end(function(Dialog $dialog){
                    return $dialog->restart();
                });
    }

    public function __exiting(Exiting $listener): void
    {
        $listener->onCancel(function(Dialog $dialog){
            $dialog->say()
                ->info('取消操作');
            return $dialog->goStage('continue');

        })->onFulfill(function(Dialog $dialog){
            $dialog->say()
                ->info('结束可配置模块的操作');
            return null;
        });
    }


}