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

use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\NLU\NLUManager;
use Commune\Components\HeedFallback\Constants\HeedFallbackLang;
use Commune\Components\HeedFallback\Data\FallbackStrategyInfo;
use Commune\Components\HeedFallback\Data\StrategyMatcherOption;
use Commune\Components\HeedFallback\Libs\FallbackStrategyManager;
use Commune\Components\HeedFallback\Support\HeedFallbackUtils;
use Commune\Ghost\IMindDef\IIntentDef;
use Commune\Ghost\Support\ContextUtils;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @mixin LesionTask
 */
trait LesionTrait
{
    protected $scopes = [
        HeedFallbackLang::STRATEGY_SCOPE_STAGE,
        HeedFallbackLang::STRATEGY_SCOPE_CONTEXT,
        HeedFallbackLang::STRATEGY_SCOPE_INTENT,
    ];

    /**
     * @title 归档意图策略
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_archive(StageBuilder $stage) : StageBuilder
    {

        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {

                $routes = $this->getCandidateRoutes();
                $cloner = $dialog->cloner;
                $choices = array_map(function(Ucl $ucl) use ($cloner){
                    $intentTitle = $ucl->findIntentDef($cloner)->getTitle();
                    $intentName = $ucl->getIntentName();
                    if ($intentName === $intentTitle) {
                        return $intentName;
                    } else {
                        return "$intentTitle ($intentName)";
                    }
                }, $routes);

                $choices = array_merge(
                    $choices,
                    [
                        's|查找或创建意图' => $this->getStage('search_intent'),
                        'b|返回' => $this->getStage('menu'),
                    ]
                );

                return $dialog
                    ->await()
                    ->withQuestion(function(QuestionMsg $question) {
                        $question->withoutMatchMode(
                            QuestionMsg::MATCH_SUGGESTION
                        );
                    })
                    ->withSlots([
                        'text' => $this->scene->text,
                    ])
                    ->askVerbal(
                        HeedFallbackLang::REQUIRE_INTENT_NAME,
                        $choices
                    );
            })
            ->onReceive(function(Dialog $dialog) {


                return $dialog
                    ->hearing()
                    ->isAnswered()
                    ->then(function(Dialog $dialog, AnswerMsg $isAnswered) {
                        $routes = $this->getCandidateRoutes();
                        $choice = $isAnswered->getChoice();
                        $answer = $isAnswered->getAnswer();

                        // 按理选择的一定是意图, 才能走到这里.
                        $route = $routes[$choice] ?? null;
                        if (empty($route)) {

                            if (!is_numeric($answer)) {
                                $this->createIntent = $answer;
                                return $dialog->goStage('search_intent');
                            }


                            return $dialog
                                ->send()
                                ->notice(HeedFallbackLang::SELECTED_INTENT_NOT_FOUND, ['intent' => $answer])
                                ->over()
                                ->rewind();
                        }

                        // 确定了意图.
                        $this->selectedIntent = $route->getIntentName();
                        return $dialog->goStage('confirm_intent');

                    })
                    ->end();

            });
    }


    /**
     * @title 搜索或新建意图
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_search_intent(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onRedirect(function(){
                $createIndex = $this->createIntent;
                if (isset($createIndex)) {
                    $this->intentChoices = $this->searchIntentIds($createIndex);
                } else {
                    $this->intentChoices = [];
                }

                return null;
            })
            ->onActivate(function(Dialog $dialog) {

                $createIntent = $this->createIntent;

                $defs = $this->getIntentChoiceDefs();

                $choices = array_map(function(IntentDef $def) {
                    $name = $def->getIntentName();
                    $title = $def->getDescription();
                    if ($name === $title) {
                        return $name;
                    }

                    return "$title ($name)";
                }, $defs);

                $choices['b|返回'] = $this->getStage('menu');
                $choices['s'] = '继续搜索';

                if (!empty($createIntent)) {
                    $choices["c|创建意图 $createIntent"] = $this->getStage('create_intent');
                }

                return $dialog
                    ->await()
                    ->withQuestion(function(QuestionMsg $question) {
                        $question->withoutMatchMode(
                            QuestionMsg::MATCH_SUGGESTION
                        );
                    })
                    ->askVerbal(
                        HeedFallbackLang::REQUIRE_SEARCH_INTENT,
                        $choices
                    );

            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isChoice('s')
                    ->then(function(Dialog $dialog) {
                        $this->intentChoices = [];
                        $this->createIntent = null;
                        return $dialog->reactivate();
                    })
                    ->isAnswered()
                    ->then(function(Dialog $dialog, AnswerMsg $isAnswered) {
                        $defs = $this->getIntentChoiceDefs();
                        $choice = $isAnswered->getChoice();
                        $def = $defs[$choice] ?? null;

                        if (!empty($def)) {
                            $this->selectedIntent = $def->getIntentName();
                            return $dialog->goStage('confirm_intent');
                        }

                        $text = StringUtils::normalizeString($isAnswered->getAnswer());

                        if (empty($text) || !ContextUtils::isValidIntentName($text)) {
                            return $dialog
                                ->send()
                                ->notice(HeedFallbackLang::SELECTED_INTENT_NOT_FOUND,
                                [
                                  'intent' => $text
                                ])
                                ->over()
                                ->rewind();
                        }

                        $this->intentChoices = $ids = $this->searchIntentIds($text);
                        if (!in_array($text, $ids)) {
                            $this->createIntent = $text;
                        }
                        return $dialog->reactivate();
                    })
                    ->end();

            });
    }

    protected function searchIntentIds(string $search) : array
    {

        $intentReg = $this->getCloner()->mind->intentReg();
        return $intentReg->searchIds($search, 0, 5);
    }

    /**
     * @return IntentDef[]
     */
    protected function getIntentChoiceDefs() : array
    {
        $intentReg = $this->getCloner()->mind->intentReg();
        $intentChoices = $this->intentChoices ?? [];

        $defs = [];

        foreach ($intentChoices as $intent) {
            if ($intentReg->hasDef($intent)) {
                $defs[] = $intentReg->getDef($intent);
            }
        }

        return $defs;
    }


    /**
     * @title 确认所选意图
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_confirm_intent(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {
                $intent = $this->selectedIntent;
                if (empty($intent)) {
                    return $dialog->send()
                        ->error("selected intent not found")
                        ->over()
                        ->goStage('archive');
                }

                return $dialog
                    ->await()
                    ->withSlots(['selected' => $intent])
                    ->askConfirm(
                        HeedFallbackLang::CONFIRM_INTENT
                    );
            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog->hearing()
                    ->isPositive()
                    ->then(function(Dialog $dialog, NLUManager $manager) {

                        $intent = $this->selectedIntent;
                        $def = $dialog->cloner->mind->intentReg()->getDef($intent);
                        $def->appendExample($this->scene->text);

                        $error = $manager->saveMeta($dialog->cloner, $def->toMeta());
                        if (isset($error)) {
                            return $dialog
                                ->send()
                                ->error($error)
                                ->over()->rewind();
                        }

                        return $dialog->goStage('create_strategy');
                    })
                    ->isNegative()
                    ->then($dialog->backStep())
                    ->end();
            });
    }

    /**
     * @title 创建新意图
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_create_intent(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {
                $createIntent = $this->createIntent;
                return $dialog
                    ->await()
                    ->withSlots([
                        'intent' => $createIntent
                    ])
                    ->askVerbal(
                        HeedFallbackLang::REQUIRE_INTENT_CREATE,
                        [
                            'b' => $this->getStage('search_intent'),
                        ]
                    );
            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog->hearing()
                    ->isAnswered()
                    ->then(function(Dialog $dialog, AnswerMsg $isAnswered) {
                        $answer = $isAnswered->getAnswer();
                        if (empty($answer)) {
                            return $dialog
                                ->send()
                                ->error("title should not be empty")
                                ->over()->rewind();
                        }

                        $intentDef = new IIntentDef([
                            'name' => $this->createIntent,
                            'title' => $answer,
                        ]);

                        $example = $this->scene->text;
                        $intentDef->appendExample($example);
                        $error = $dialog->cloner->nlu->saveMeta(
                            $dialog->cloner,
                            $intentDef->toMeta()
                        );
                        if (isset($error)) {
                            return $dialog->send()
                                ->error($error)->over()->rewind();
                        }
                        $this->selectedIntent = $this->createIntent;
                        $this->createIntent = null;

                        return $dialog->goStage('create_strategy');
                    })
                    ->end();

            });
    }

    /**
     * @title 创建意图策略.
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_create_strategy(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog) {

                $intent = $this->selectedIntent;


                $category = $dialog->cloner
                    ->registry
                    ->getCategory(StrategyMatcherOption::class);

                $await = $this->scene->await;
                $ucl = Ucl::decode($await);
                $ids = HeedFallbackUtils::makeAllStrategyIds($intent, $ucl->contextName, $ucl->stageName);
                foreach ($ids as $id) {
                    if ($category->has($id)) {
                        return $dialog
                            ->send()
                            ->info(
                                HeedFallbackLang::STRATEGY_EXISTS,
                                [
                                    'intent' => $intent,
                                    'await' => $await,
                                    'id' => $id
                                ]
                            )->over()->goStage('learned');
                    }
                }

                $scopes = $this->scopes;
                $scopes[] = $this->getStage('done');

                return $dialog
                    ->await()
                    ->askChoose(
                        HeedFallbackLang::STRATEGY_CHOOSE_SCOPE,
                        $scopes
                    );
            })
            ->onReceive(function(Dialog $dialog) {
                return $dialog->hearing()
                    ->isAnswered()
                    ->then(function(Dialog $dialog, AnswerMsg $answer) {
                        $choice = $answer->getChoice();
                        $text = $answer->getAnswer();
                        if (!isset($choice) || !array_key_exists($choice, $this->scopes)) {
                            return $dialog
                                ->send()
                                ->error("$text is invalid!")
                                ->over()
                                ->rewind();
                        }

                        $this->strategyScope = $this->scopes[$choice];

                        return $dialog->goStage('choose_strategy');
                    })
                    ->end();

            });
    }


    /**
     * @title 选择意图策略.
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_choose_strategy(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->always([$this, 'checkSceneExists'])
            ->onActivate(function(Dialog $dialog, FallbackStrategyManager $manager) {
                $lists = $manager->listStrategies();
                $choices = array_map(function(FallbackStrategyInfo $info) {
                    $name = $info->name;
                    $desc = $info->desc;
                    return "$name: $desc";
                }, $lists);
                $choices = array_values($choices);

                return $dialog
                    ->await()
                    ->askChoose(
                        HeedFallbackLang::STRATEGY_CHOOSE,
                        $choices
                    );
            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isAnswered()
                    ->then(function(
                        Dialog $dialog,
                        AnswerMsg $isAnswered,
                        FallbackStrategyManager $manager
                    ) {
                        $lists = $manager->listStrategies();
                        $lists = array_values($lists);
                        $choice = $isAnswered->getChoice();
                        $option = $lists[$choice] ?? null;

                        if (empty($option)) {
                            $answer = $isAnswered->getAnswer();
                            return $dialog->send()
                                ->error("option $answer not exists");
                        }

                        $cloner = $dialog->cloner;

                        $category = $cloner->registry
                            ->getCategory(StrategyMatcherOption::class);

                        $matcher = $this
                            ->createStrategyMatcher($option->strategyClass);
                        $category->save($matcher);

                        $ucl = $manager->getCreation(
                            $option->strategyClass,
                            $matcher->getId()
                        );

                        return $dialog->dependOn($ucl);
                    })
                    ->end();
            })
            ->onEvent(
                Dialog::CALLBACK,
                function(Dialog $dialog) {
                    return $dialog->goStage('learned');
                }
            );

    }

    protected function createStrategyMatcher(string $strategyName) : StrategyMatcherOption
    {
        $intent = $this->selectedIntent;
        $scope = $this->strategyScope;
        $scene = $this->scene;
        $ucl = Ucl::decode($scene->await);

        switch ($scope) {
            case HeedFallbackLang::STRATEGY_SCOPE_INTENT :
                return StrategyMatcherOption::instance(
                    $strategyName,
                    $intent
                );
            case HeedFallbackLang::STRATEGY_SCOPE_CONTEXT :
                return StrategyMatcherOption::instance(
                    $strategyName,
                    $intent,
                    $ucl->contextName
                );
            case HeedFallbackLang::STRATEGY_SCOPE_STAGE :
            default:
                return StrategyMatcherOption::instance(
                    $strategyName,
                    $intent,
                    $ucl->contextName,
                    $ucl->stageName
                );

        }

    }
}