<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindMeta;

use Commune\Ghost\IMindDef\IChatDef;
use Commune\Support\Option\AbsMeta;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $cid
 * @property string $say
 * @property string $reply
 * @property string $index
 */
class ChatMeta extends AbsMeta implements DefMeta
{
    const IDENTITY = 'cid';

    public static function stub(): array
    {
        return [
            'cid' => '',
            'say' => '',
            'reply' => '',
            'index' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }


    public function __get_wrapper() : string
    {
        return IChatDef::class;
    }

    public function __get_name() : string
    {
        return $this->cid;
    }

    public function __get_title() : string
    {
        return $this->say;
    }

    public function __get_desc() : string
    {
        return $this->say;
    }

}