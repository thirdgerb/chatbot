<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Tree\Prototype;

use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Ghost\Stage\IStageDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $name
 * @property-read string $contextName
 * @property-read string $stageName
 *
 * @property-read string[] $events
 *
 * @property-read string|null $parent
 * @property-read string[] $children
 * @property-read string|null $elder
 * @property-read string|null $younger
 *
 * @property-read IntentMeta|null $asIntent
 * @property-read string|null $ifRedirect
 */
class BranchStageDef extends IStageDef
{

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            'contextName' => '',
            'stageName' => '',

            // 爹妈
            'parent' => null,
            // 儿女
            'children' => [],
            // 哥哥姐姐
            'elder' => null,
            // 弟弟妹妹
            'younger' => null,


            'events' => [],

            'asIntent' => null,
            'ifRedirect' => null,
        ];
    }


    /**
     * 鉴定是否是完整的 def 定义.
     * 原理很简单, 检查 name / title 是否一致.
     * @return bool
     */
    public function isComplete() : bool
    {
        return $this->getTitle() !== $this->getName()
            && $this->getDescription() !== $this->getName();
    }
}