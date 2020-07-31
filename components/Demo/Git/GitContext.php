<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo\Git;

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\ACodeContext;


/**
 * 用 git 的命令作为测试工具, 来验证子进程异步任务的实现.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @desc Git 命令测试用例
 */
class GitContext extends ACodeContext
{

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption();
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage->always(function(Dialog $dialog) {
            return $dialog
                ->send()
                ->info("当前用例用于测试 CommuneChatbot 异步任务 + 子进程两个功能的.")
                ->info("系统将提示可用的 git 命令, 选择后异步执行命令, 然后将结果返回给当前会话.")
                ->over()
                ->goStage('menu');
        });

    }

    public function __on_menu(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->await()
                    ->askChoose(
                        '请选择当前可用的命令',
                        [
                            's' => '执行 git status 命令',
                            't' => '查看 git 代码行数统计',
                            'l' => '查看 git log 日志',
                            'q' => '退出当前任务',
                        ]
                    );
            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog->hearing()
                    ->isChoice('q')
                    ->then($dialog->cancel())

                    ->isChoice('s')
                    ->then($this->callGitService('status'))

                    ->isChoice('t')
                    ->then($this->callGitService('state'))

                    ->isChoice('l')
                    ->then(function(Dialog $dialog) {
                        $supervise = $dialog
                            ->cloner
                            ->auth
                            ->allow(Supervise::class);

                        if ($supervise) {
                            $dialog->send()->info('功能未实装');
                        } else {
                            $dialog->send()->error('没有权限操作!');
                        }

                        return $dialog->rewind();
                    })
                    ->end();
            });
    }

    public function callGitService(string $command) : callable
    {
        return function(Dialog $dialog) use ($command) : Operator {
            $dialog
                ->cloner
                ->dispatcher
                ->asyncService(
                    GitService::class,
                    ['command' => $command]
                );
            return $dialog
                ->send()
                ->info("发送异步任务")
                ->over()
                ->rewind();
        };
    }



}