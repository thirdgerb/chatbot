<?php


namespace Commune\Demo\App\Components\CustomService\Routes;


use Commune\Chatbot\App\Contexts\RouteDef;
use Commune\Chatbot\App\Messages\QA\Choice;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\App\Components\CustomService\Memories\UserInfo;
use Commune\Demo\App\Components\CustomService\Tasks\RandomTest;
use Commune\Demo\App\Components\CustomService\Intents\ReturnDeviceNotReceived;

/**
 * @property UserInfo $user
 */
class HearUserCall extends RouteDef
{
    protected $domain = 'Commune.Demo.App.Components.CustomService';

    /**
     * @var string
     */
    protected $askNeedMore = 'ask.needMore';

    protected $routes = [
        ReturnDeviceNotReceived::class ,
        RandomTest::class,
    ];

    final public function __construct()
    {
        parent::__construct(
            $this->routes,
            $this->domain,
            '',
            '请问您有什么需求?',
            true
        );
    }

    public static function __depend(Depending $depending): void
    {
        $depending->onMemory('user', UserInfo::class);
    }

    public function __onStart(Stage $stage) : Navigator
    {
        $dialog = $stage->dialog;
        $this->sayWelcome($dialog);
        return $dialog->goStage('route');
    }

    public function sayWelcome(Dialog $dialog): void
    {
        $talk = $dialog->say()->withContext($this->user);
        if ($this->user->vip) {
            $talk->info("您好, %name%! 欢迎使用客服");
        } else {
            $talk->info("您好, %name%! VIP客服为您服务!");
        }
    }

    public function __onRedirect(Stage $stage): Navigator
    {
        return $stage->dependOn($this->routeTo, [$this, 'redirect']);
    }

    public function redirect(Dialog $dialog) : Navigator
    {
        return $dialog->goStage('more');
    }

    public function __onMore(Stage $stage) : Navigator
    {
        return $stage->talk(function (Dialog $dialog) {
            $dialog->say()
                ->withContext($this)
                ->askConfirm($this->askNeedMore, false);

        }, function (Dialog $dialog, Message $message) : Navigator{

            return $dialog->hear($message)
                ->isChoice(1, function(Dialog $dialog){
                    return $dialog->goStage('route');
                })
                ->isChoice(0, function(Dialog $dialog){
                    return $dialog->goStage('then');
                })
                ->end();
        });
    }



    public function __onThen(Stage $stageRoute): Navigator
        {
            return $stageRoute->talk(function(Dialog $dialog){
                $dialog->say()
                    ->withContext($this)
                    ->askChoose(
                        '请您对我做出评价:',
                        [
                            '非常好',
                            '一般般',
                            '没解决问题'
                        ],
                        0
                    );
                return $dialog->wait();

            }, function(Dialog $dialog, Message $message){
                return $dialog->hear($message)
                    ->isAnswer(function(Dialog $dialog, Choice $choice){
                        $dialog->say()
                            ->info("(假装记录了评分: ".$choice->toResult().')')
                            ->info("谢谢您的评价!");

                        return $this->farewell($dialog);
                    })
                    ->end(function(Dialog $dialog){
                        $dialog->say()
                            ->info("(没有评分, 假装没看到)");
                        return $this->farewell($dialog);
                    });

            });
        }

    protected function farewell(Dialog $dialog) : Navigator
    {
        $dialog->say()->info("再见! 欢迎您随时再来");
        return $dialog->fulfill();
    }

    public function __exiting(Exiting $listener): void
    {
    }


}