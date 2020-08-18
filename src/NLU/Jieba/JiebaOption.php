<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\Jieba;

use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $mode
 * @property-read string $dict
 * @property-read string $cjk
 *
 * @property-read string $stopWordsFile
 */
class JiebaOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'mode'=>'default',
            'dict'=>'normal',
            'cjk'=>'chinese',
            'stopWordsFile' => __DIR__ . '/stop.txt',
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}