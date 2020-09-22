<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\Managers;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Components\SpaCyNLU\NLU\SpaCyNLUService;
use Commune\Ghost\Context\ACodeContext;
use Commune\Ghost\Context\Command\CancelCmdDef;
use Commune\Ghost\Context\Command\QuitCmdDef;
use Commune\Message\Host\Convo\Verbal\JsonMsg;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title SpaCyNLU 意图模块管理
 */
class NLUServiceManager extends ACodeContext
{

    protected static $_command_mark = '.';

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'strategy' => [
                'comprehendPipes' => [], //设置为没有.
                'heedFallbackStrategies' => [],
                'commands' => [
                    CancelCmdDef::class,
                    QuitCmdDef::class,
                ]
            ]
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {

                // todo 回头提供菜单.
                return $dialog
                    ->send()
                    ->info("进入 SpaCy NLU 意图匹配模块")
                    ->over()
                    ->await()
                    ->askChoose(
                        "请选择想要测试的功能 (输入 !help 查看命令) :",
                        [
                            $this->getStage('sync_mind'),
                            $this->getStage('nlu_test'),
                        ]
                    );
            });
    }

    /**
     * @title 同步语料库
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_sync_mind(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog, SpaCyNLUService $service) {

                $error = $service->syncMind($dialog->cloner->mind);
                if (isset($error)) {
                    return $dialog
                        ->send()
                        ->error($error)
                        ->over()
                        ->goStage('start');
                }

                return $dialog
                    ->send()
                    ->info('ok')
                    ->over()
                    ->goStage('start');

            });

    }


    /**
     * @title 意图理解测试
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_nlu_test(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                return $dialog
                    ->send()
                    ->info("请输入想要测试的句子, 返回匹配结果. ")
                    ->over()
                    ->await();
            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->pregMatch('/^\s*\./')
                        ->then($dialog->confuse())
                    ->isVerbal()
                        ->then(function(Dialog $dialog, SpaCyNLUService $service) {

                            $comprehension = $service->parse(
                                $dialog->input,
                                $dialog->cloner,
                                $dialog->cloner->comprehension
                            );

                            $data = $comprehension->intention->getPossibleIntentData();
                            $json = JsonMsg::instance(
                                json_encode(
                                    $data,
                                    ArrayAndJsonAble::PRETTY_JSON
                                )
                            );

                            return $dialog
                                ->send()
                                ->message($json)
                                ->over()
                                ->rewind();
                        })
                    ->end();
            });
    }



}