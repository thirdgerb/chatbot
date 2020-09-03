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
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\NLU\SimpleChat;
use Commune\Components\HeedFallback\Constants\HeedFallbackLang;
use Commune\Components\HeedFallback\Data\FallbackSceneOption;
use Commune\Components\HeedFallback\HeedFallbackComponent;
use Commune\Components\HeedFallback\Libs\FallbackSceneRepository;
use Commune\Ghost\Context\ACodeContext;
use Commune\Ghost\IMindDef\IChatDef;
use Commune\Message\Host\Convo\Verbal\ReplyMsg;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Support\Utils\StringUtils;


/**
 * 教机器人怎么做. 理论上只允许管理员操作.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 教对话机器人基本回复策略.
 *
 * @property-read string $batchId  来自query
 *
 *
 * @property string|null $selectedIntent
 * @property string[] $intentChoices
 * @property string|null $createIntent
 * @property string|null $strategyScope
 *
 * # getters
 * @property-read FallbackSceneOption|null $scene
 * @property-read FallbackSceneRepository $repo
 */
class LesionTask extends ACodeContext
{
    use LesionTrait;

    /**
     * @var FallbackSceneOption|null
     */
    protected $_scene;

    /**
     * @var FallbackSceneRepository|null
     */
    protected $_repo;

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'priority' => 0,
            'queryNames' => ['batchId'],
            'memoryScopes' => [],
            'memoryAttrs' => [
                'selectedIntent' => null,
                'intentChoices' => [],
                'createIntent' => null,
                'strategyScope' => null,
            ],
            'strategy' => [
                'auth' => [Supervise::class],
                'comprehendPipes' => null,
                'onCancel' => 'cancel',
                'onQuit' => 'quit',
                'stageRoutes' => [
                    'menu',
                ],
                'contextRoutes' => [],
                'heedFallbackStrategies' => [],
            ],
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function checkSceneExists(Dialog $dialog) : ? Operator
    {
        $scene = $this->scene;
        if (empty($scene)) {
            return $dialog
                ->send()
                ->error(
                    HeedFallbackLang::TEACH_TASK_NOT_FOUND,
                    [
                        'batchId' => $this->batchId
                    ]
                )
                ->over()
                ->goStage('cancel');
        }
        return null;
    }

    /**
     * @title 入口校验.
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->always($stage->dialog->goStage('brief'));

    }


    /**
     * @title 任务场景概述
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_brief(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {

                $cloner = $dialog->cloner;
                $scene = $this->scene;
                $await = $scene->await;
                if (empty($await)) {
                    $context = '';
                    $stage = '';
                } else {
                    $ucl = Ucl::decode($await);
                    $context = $ucl->findContextDef($cloner)->getTitle();
                    $stage = $ucl->findStageDef($cloner)->getTitle();
                }

                $text = $scene->text;
                $possible = $scene->possibleIntents;
                $matched = $possible[0] ?? '';

                return $dialog
                    ->send()
                    ->info(
                        HeedFallbackLang::FALLBACK_SCENE_BRIEF,
                        [
                            'await' => $await,
                            'context' => $context,
                            'stage' => $stage,
                            'text' => $text,
                            'matched' => $matched
                        ]
                    )
                    ->over()
                    ->goStage('menu');
            });
    }


    /**
     * @title 选择菜单
     * @spell #menu
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_menu(StageBuilder $stage) : StageBuilder
    {

        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {

                $choices = [
                    // 意图策略
                     $this->getStage('archive'),
                    // 直接回复
                    $this->getStage('reply'),
                    // 跳过
                    $this->getStage('skip'),
                    // 闲聊策略
                    $this->getStage('chat'),
                    // 忽略掉
                    $this->getStage('ignore'),
                    // 退出
                    $this->getStage('cancel'),
                ];

                return $dialog
                    ->await()
                    ->askChoose(
                        HeedFallbackLang::REQUIRE_OPERATION,
                        $choices
                    );

        });
    }



    /*-------- candidate ---------*/

    /**
     * @return Ucl[]
     */
    protected function getCandidateRoutes() : array
    {
        $possibles = $this->scene->possibleIntents;
        $sceneRoutes = $this->scene->routes;
        $globalRoutes = $this->getCloner()->config->globalContextRoutes;

        $routes = [];
        $this->fillRoutes($routes, $possibles);
        $this->fillRoutes($routes, $sceneRoutes);
        $this->fillRoutes($routes, $globalRoutes);
        return array_values($routes);
    }

    protected function fillRoutes(array &$routes, array $uclStr) : void
    {
        foreach ($uclStr as $str) {
            $ucl = Ucl::decode($str);
            if ($ucl->isValidPattern()) {
                $routes[$ucl->getStageFullname()] = $ucl;
            }
        }
    }

    /*-------- skip ---------*/

    /**
     * @title 跳过这个任务
     * @desc 暂时跳过
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_skip(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {
                return $dialog->goStage('cancel');
            });
    }

    /**
     * @title 忽略
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_ignore(StageBuilder $stage) : StageBuilder
    {
        return $stage->always($stage->dialog->goStage('done'));
    }

    /**
     * @title 人工回复
     * @desc 人工回复 (不学习)
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_reply(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->await()
                    ->askVerbal(
                        HeedFallbackLang::REQUIRE_DIRECT_REPLY,
                        [
                            'b|返回' => $this->getStage('menu'),
                        ]
                    );

            })->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isAnswered()
                    ->then(function(Dialog $dialog, AnswerMsg $isAnswered){
                        $answer = $isAnswered->getAnswer();
                        if (StringUtils::isEmptyStr($answer)) {
                            return $dialog->send()
                                ->error("reply should not be empty")
                                ->over()
                                ->rewind();
                        }

                        $scene = $this->scene;
                        $message = ReplyMsg::instance(
                            $scene->text,
                            $answer
                        );

                        return $dialog
                            ->send()
                            ->withSessionId($scene->sessionId, true)
                            ->message($message)
                            ->over()
                            ->goStage('done');
                    })
                    ->end();
            });
    }

    /*-------- 闲聊定位 ---------*/

    /**
     * @title 使用闲聊
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_chat(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {
                // 如果没有闲聊服务.
                $cloner = $dialog->cloner;
                $nlu = $cloner->nlu;
                $chat = $nlu->getService($cloner, SimpleChat::class);
                if (empty($chat)) {
                    return $dialog
                        ->send()
                        ->notice(HeedFallbackLang::CHAT_MODULE_NOT_FOUND)
                        ->over()
                        ->goStage('menu');
                }

                /**
                 * @var SimpleChat $chat
                 */
                $reply = $chat->reply($this->scene->text, HeedFallbackComponent::class);

                return $dialog
                    ->send()
                    ->info(HeedFallbackLang::SIMPLE_CHAT_REPLY,
                        ['reply' => $reply]
                    )
                    ->over()
                    ->await()
                    ->askVerbal(
                        HeedFallbackLang::SIMPLE_CHAT_CONFIRM,
                        [
                            'c|confirm' => $this->getStage('done'),
                            'b|返回' => $this->getStage('menu'),
                        ]
                    );
            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isAnswered()
                    ->then(function(Dialog $dialog, AnswerMsg $isAnswered) {
                        $text = $isAnswered->getAnswer();
                        if (StringUtils::isEmptyStr($text)) {
                            return $dialog
                                ->send()
                                ->error('reply is empty')
                                ->over()
                                ->rewind();
                        }

                        $def = new IChatDef(
                            $this->scene->text,
                            $text,
                            HeedFallbackComponent::class
                        );

                        $cloner = $dialog->cloner;
                        $cloner
                            ->nlu
                            ->asyncSaveMeta($cloner, $def->toMeta());

                        $message = ReplyMsg::instance(
                            $this->scene->text,
                            $text
                        );

                        $scene = $this->scene;
                        return $dialog
                            ->send()
                            ->withSessionId($scene->sessionId, true)
                            ->message($message)
                            ->over()
                            ->goStage('done');
                    })
                    ->end();
            });
    }

    public function __on_learned(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {

                $session = $this->scene->sessionId;
                $text = $this->scene->text;
                return $dialog
                    ->send()
                    ->withSessionId($session, true)
                    ->info(
                        HeedFallbackLang::STRATEGY_LEARNED,
                        [
                            'text' => $text
                        ]
                    )
                    ->over()
                    ->goStage('done');
            });

    }

    /**
     * @title 完成任务.
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_done(StageBuilder $stage) : StageBuilder
    {
        return $stage->always(function(Dialog $dialog) {
            $this->repo->delete($this->batchId);
            return $dialog
                ->send()
                ->info('done')
                ->over()
                ->fulfill();
        });
    }

    /*-------- 异常退出 ---------*/

    /**
     * @title 退出教学
     * @param StageBuilder $stage
     * @return StageBuilder
     * @spell 退出
     */
    public function __on_cancel(StageBuilder $stage) : StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog) {
            $this->rePushScene();
            return $dialog
                ->send()
                ->notice('cancel')
                ->over()
                ->cancel();

        });
    }

    protected function rePushScene() : void
    {
        $scene = $this->scene;
        if (isset($scene)) {
            $this->repo->push($scene);
        }
    }

    public function __on_quit(StageBuilder $stage) : StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog) {
            $this->rePushScene();
            return $dialog->quit();
        });
    }


    /*-------- getters ---------*/



    public function __get_scene() : ? FallbackSceneOption
    {
        if (isset($this->_scene)) {
            return $this->_scene;
        }
        return $this->_scene = $this->repo->find($this->batchId);
    }

    public function __get_repo() : FallbackSceneRepository
    {
        return $this->_repo
            ?? $this->_repo = $this
                ->getCloner()
                ->container
                ->make(FallbackSceneRepository::class);

    }

}