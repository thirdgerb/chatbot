<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint;

use Commune\Support\Struct\Structure;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 *
 * @property-read string $shellName Shell的名称
 *
 * @property-read string[] $pipeline Shell运行的管道
 * @property-read string[] $directives Shell 预加载的命令. id => DirectiveClass
 *
 * @property-read string[] $babel  type => serializable
 */
class ShellConfig extends Structure
{

    const IDENTITY = 'shellName';

    public static function stub(): array
    {
        return [];
    }


}