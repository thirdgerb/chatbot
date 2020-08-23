<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Context;

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Components\HeedFallback\Constants\HeedFallbackLang;
use Commune\Components\HeedFallback\Libs\FallbackSceneRepository;
use Commune\Ghost\Context\ACodeContext;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 查看对话机器人的教学任务
 */
class TeachTasks extends ACodeContext
{
    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'priority' => 0,
            'queryNames' => [],
            'memoryScopes' => [],
            'memoryAttrs' => [],
            'strategy' => [
                'auth' => [Supervise::class],
                'comprehendPipes' => null,
                'stageRoutes' => [],
                'contextRoutes' => [],
                'heedFallbackStrategy' => [],
            ],
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog, FallbackSceneRepository $repo) {

                $count = $repo->count();

                // 描述任务.
                $dialog->send()
                    ->info(HeedFallbackLang::TOTAL_TASKS, ['count' => $count])
                    ->over();

                if ($count > 0) {
                    return $dialog
                        ->await()
                        ->askConfirm(
                            HeedFallbackLang::CONFIRM_START_TASK
                        );
                } else {
                    return $dialog->goStage('operates');
                }


            })->onReceive(function(Dialog $dialog) {
                return $dialog
                    ->hearing()
                    ->isPositive()
                    ->then(function(Dialog $dialog, FallbackSceneRepository $repo) {
                        $task = $repo->pop();
                        if (empty($task)) {
                            return $dialog
                                ->send()
                                ->notice(HeedFallbackLang::TASK_DISAPPEAR)
                                ->over()
                                ->reactivate();
                        }

                        return $dialog->blockTo(
                            LesionTask::genUcl(['batchId' => $task->batchId])
                        );

                    })
                    ->isNegative()
                    ->then(function(Dialog $dialog) {
                        return $dialog->goStage('operates');
                    })
                    ->end();
            });

    }

    public function __on_operates(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog) {
            return $dialog
                ->await()
                ->askChoose(
                    HeedFallbackLang::REQUIRE_OPERATION,
                    [
                        $this->getStage('cancel'),
                        $this->getStage('repeat'),
                        $this->getStage('flush'),
                    ]
                );
        });
    }

    /**
     * @title 清空
     *
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_flush(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog, FallbackSceneRepository $repo) {
            $repo->flush();
            return $dialog->goStage('start');

        });
    }


    /**
     * @title 退出
     *
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_cancel(StageBuilder $stage): StageBuilder
    {
        return $stage->always($stage->dialog->cancel());
    }


    /**
     * @title 再看看
     *
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_repeat(StageBuilder $stage): StageBuilder
    {
        return $stage->always($stage->dialog->goStage('start'));
    }

}