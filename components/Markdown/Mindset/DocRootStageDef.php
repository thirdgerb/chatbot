<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Mindset;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IMindDef\IIntentDef;
use Commune\Ghost\Stage\AbsStageDef;
use Commune\Message\Host\Convo\Verbal\MarkdownMsg;
use Commune\Support\Utils\StringUtils;


/**
 * Markdown 文档的起点语境. 考虑到可能 markdown文档一个标题也没有
 * 所以无法完全按树形结构来描述.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $contextName
 * @property-read string $stageName
 * @property-read IntentMeta|null $asIntent
 *
 *
 * @property-read string $document
 * @property-read string|null $nextStage
 *
 */
class DocRootStageDef extends AbsStageDef
{

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',

            'contextName' => '',
            'stageName' => '',
            'asIntent' => null,

            'document' => '',
            'nextStage' => null,
        ];
    }


    public function asIntentDef(): IntentDef
    {
        // 空节点. 不希望被指定.
        return new IIntentDef([
            'name' => $this->name,
            'title' => $this->title,
            'desc' => $this->desc,
        ]);
    }

    protected function always(Dialog $dialog) : Operator
    {
        $doc = $this->document;
        if (!StringUtils::isEmptyStr($doc)) {
            $dialog
                ->send()
                ->message(MarkdownMsg::instance($doc))
                ->over();
        }

        return $dialog->next($this->nextStage);
    }

    public function onActivate(Activate $dialog): Operator
    {
        return $this->always($dialog);
    }

    public function onReceive(Receive $dialog): Operator
    {
        return $this->always($dialog);
    }

    public function onRedirect(Dialog $prev, Ucl $current): ? Operator
    {
        return null;
    }

    public function onResume(Resume $dialog): ? Operator
    {
        return null;
    }


}