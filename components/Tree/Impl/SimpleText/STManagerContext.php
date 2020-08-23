<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Tree\Impl\SimpleText;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Ghost\Predefined\Services\MindsetSaveService;
use Commune\Ghost\Predefined\Services\Payload\MindSavePayload;
use Commune\Ghost\Predefined\Services\Payload\TransSavePayload;
use Commune\Ghost\Predefined\Services\TranslationSaveService;
use Commune\Contracts\Trans\Translator;
use Commune\Ghost\Context\ACodeContext;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Support\Utils\ArrayUtils;


/**
 * SimpleText 模块的管理工具.
 * 编辑 translator 的回复作为对话的内容.
 *
 * @title SimpleTextTree 编辑器
 * @desc 编辑当前语境
 * @spell 编辑
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $stageName
 * @property string $title
 * @property string $desc
 * @property string $text
 */
class STManagerContext extends ACodeContext
{
    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([

            // context 的优先级. 若干个语境在 blocking 状态中, 根据优先级决定谁先恢复.
            'priority' => 1,

            // context 的默认参数名, 类似 url 的 query 参数.
            // query 参数值默认是字符串.
            // query 参数如果是数组, 则定义参数名时应该用 [] 做后缀, 例如 ['key1', 'key2', 'key3[]']
            'queryNames' => [
                'stageName'
            ],

            // memory 记忆体的默认值.
            'memoryAttrs' => [
                'title' => '',
                'desc' => '',
                'text' => '',
            ],
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        $name = $this->stageName;
        return $stage->always(
            $stage
                ->dialog
                ->send("开始编辑对话节点 [$name]")
                ->over()
                ->goStage('title')
        );
    }

    protected function getStageDef(Dialog $dialog) :StageDef
    {
        return $dialog->cloner->mind->stageReg()->getDef($this->stageName);
    }

    protected function getStageAndValue(Dialog $dialog) : array
    {
        $def = $this->getStageDef($dialog);
        $name = $dialog->ucl->stageName;
        $current = '';
        $value = $this->__get($name);
        switch($name) {
            case 'title':
                $current = $def->getTitle();
                break;
            case 'desc':
                $current = $def->getDescription();
                break;
            case 'text':
                /**
                 * @var Translator $translator
                 */
                $translator = $dialog->container()->make(Translator::class);
                $current = $translator->trans($def->getName());
                break;
        }
        return [$name, $current, $value];
    }

    protected function getChoices(string $stageName) : array
    {
        $choices = [
            'title' => $this->getStage('title'),
            'desc' => $this->getStage('desc'),
            'text' => $this->getStage('text'),
            'final' => $this->getStage('final'),
        ];

        unset($choices[$stageName]);
        return array_values($choices);
    }

    protected function getNext(string $stage) : string
    {
        $list = ['title', 'desc', 'text', 'final'];
        return ArrayUtils::nextTo($list, $stage);
    }


    protected function modifyStage(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                list($name, $current, $value) = $this->getStageAndValue($dialog);
                $stage = $this->stageName;
                return $dialog
                    ->send()
                    ->info("正在编辑 stage $stage 的 [$name]\n当前值为: $current\n修改中的值为: $value")
                    ->over()
                    ->await()
                    ->askVerbal(
                        "请输入 [$name] 新的值, 或者: ",
                        $this->getChoices($name)
                    );

            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isAnswered()
                    ->then(function(AnswerMsg $isAnswered, Dialog $dialog){
                        $value = $isAnswered->getText();
                        $name = $dialog->ucl->stageName;
                        $this->__set($name, $value);

                        $name = $dialog->ucl->stageName;
                        $next = $this->getNext($name);
                        return $dialog->send()
                            ->info("[$name] 修改为: $value")
                            ->over()
                            ->goStage($next);

                    })
                    ->end();
            });

    }

    /**
     *
     * @desc 修改 stage 标题
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_title(StageBuilder $stage) : StageBuilder
    {
        return $this->modifyStage($stage);
    }

    /**
     * @desc 修改 stage 简介
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_desc(StageBuilder $stage) : StageBuilder
    {
        return $this->modifyStage($stage);
    }

    /**
     * @desc 修改 stage 对话内容
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_text(StageBuilder $stage) : StageBuilder
    {
        return $this->modifyStage($stage);
    }

    /**
     * @desc 保存改动.
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_final(StageBuilder $stage) : StageBuilder
    {

        return $stage->always(function(Dialog $dialog) {

            $stage = $this->stageName;
            $title = $this->title;
            $desc = $this->desc;
            $text = $this->text;
            $def = $this->getStageDef($dialog);

            $data = $def->toMeta()->toArray();
            $data['title'] = $this->title;
            $data['desc'] = $this->desc;

            $dispatcher = $dialog->cloner->dispatcher;

            // save meta
            $dispatcher->asyncService(
                MindsetSaveService::class,
                MindSavePayload::create([
                    'metaName' => StageMeta::class,
                    'metaData' => $data
                ])->toArray()
            );

            $messages = [$stage => $text];
            $dispatcher->asyncService(
                TranslationSaveService::class,
                TransSavePayload::create(['messages' => $messages])
                    ->toArray()
            );

            return $dialog->send()
                ->info("保存 [$stage]的数据\ntitle: $title\ndesc: $desc\ntext: $text")
                ->over()
                ->fulfill();
        });
    }

}