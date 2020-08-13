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

use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Ghost\Stage\IStageDef;


/**
 * Section 段落形成的 stage.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $name
 * @property string $title
 * @property string $desc
 *
 *
 * @property string $contextName
 * @property string $stageName
 *
 * @property-read string[] $events
 *
 * @property string $groupName
 *
 * @property string $orderId
 * @property string|null $parent
 * @property string[] $children
 * @property string|null $elder
 * @property string|null $younger
 * @property int $depth
 *
 * @property IntentMeta $asIntent
 * @property string|null $ifRedirect
 */
class SectionStageDef extends IStageDef
{

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',

            // 通常也就是 markdown id
            'contextName' => '',
            // 默认是 order id.
            'stageName' => '',

            // 还需要一个 group 参数.
            'groupName' => '',

            // 树结构. 参考 BranchStageDef
            // 文档内序号位置. root_0_1_3_2_4
            'orderId' => '',
            // 爹妈
            'parent' => null,
            // 儿女
            'children' => [],
            // 哥哥姐姐
            'elder' => null,
            // 弟弟妹妹
            'younger' => null,
            // 深度. 和标题级别不一定一致.
            'depth' => 0,

            // stage events
            'events' => [],
            'asIntent' => [],

            'ifRedirect' => null,
        ];
    }



}