<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef\Intent;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $name              entity 的名称
 *
 * @property string $value             entity 的值
 *
 * @property int $start                entity 的开始位置
 * @property int $width                entity 的结束位置
 *
 * @property string $left              entity 左边的正常内容.
 * @property string $right             entity 右边的正常内容.
 *
 * @property ExampleEntity|null $next  下一个 Entity.
 */
interface ExampleEntity
{

}