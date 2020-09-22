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
use Commune\Components\SpaCyNLU\NLU\SpaCySimpleChat;
use Commune\Ghost\Context\ACodeContext;
use Commune\Ghost\Context\Command\CancelCmdDef;
use Commune\Ghost\Context\Command\QuitCmdDef;
use Commune\Ghost\IMindDef\IChatDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SimpleChatManager extends ACodeContext
{
    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'strategy' => [
                'comprehendPipes' => [], //设置为没有.
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
                return $dialog
                    ->send()
                    ->info("管理 SpaCy NLU 闲聊模块")
                    ->over()
                    ->await()
                    ->askChoose(
                        "请选择操作 (输入 !help 查看指令) :",
                        [
                            $this->getStage('sync_mind'),
                            $this->getStage('chat'),
                            $this->getStage('learn'),
                        ]
                    );
            });
    }

    /**
     * @title 教学
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_learn(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->send()
                    ->info("教机器人说话, 用  `对话|回复|索引`  的格式来表示. 索引非必须:")
                    ->over()
                    ->await();
            })
            ->onReceive(function(Dialog $dialog) {
                return $dialog
                    ->hearing()
                    ->isVerbal()
                    ->then(function(Dialog $dialog, SpaCySimpleChat $service) {
                        $text = $dialog->input->getMessage()->getText();

                        $parts = explode('|', $text);
                        $say = $parts[0];
                        $reply = $parts[1] ?? '';
                        $index = $parts[2] ?? '';

                        if (empty($say) || empty($reply)) {
                            return $dialog
                                ->send()
                                ->error("用户对话和回复不能为空, 用 | 隔开")
                                ->over()
                                ->rewind();
                        }

                        $def = new IChatDef(
                            $say,
                            $reply,
                            $index
                        );

                        $cloner = $dialog->cloner;
                        $cloner->nlu->asyncSaveMeta($cloner, $def->toMeta());

                        return $dialog
                            ->send()
                            ->info('async save chat')
                            ->over()
                            ->goStage('start');
                    })
                    ->end();

            });
    }


    /**
     * @title 闲聊
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_chat(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->await()
                    ->askVerbal("请输入对白, 让机器人回答 (输入 !help 查看命令) :");
            })
            ->onReceive(function(Dialog $dialog) {
                return $dialog
                    ->hearing()
                    ->isVerbal()
                    ->then(function(Dialog $dialog, SpaCySimpleChat $service) {

                        $text = $dialog->input->getMessage()->getText();
                        $reply = $service->reply($text) ?? 'NULL';

                        return $dialog
                            ->send()
                            ->info("回答为: $reply")
                            ->over()
                            ->rewind();
                    })
                    ->end();

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
            ->onActivate(function(Dialog $dialog, SpaCySimpleChat $service) {
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


}