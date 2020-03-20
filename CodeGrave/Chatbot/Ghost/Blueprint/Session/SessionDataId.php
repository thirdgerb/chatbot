<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint\Session;

/**
 * 可以在 Session 中存取的数据是 SessionData
 * SessionData 通过 SessionDataId 来获取, 或被其它 SessionData 持有.
 * 相当于 SessionData 的指针.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $type
 */
class SessionDataId
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * SessionDataId constructor.
     * @param string $id
     * @param string $type
     */
    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function __get($name)
    {
        return $this->{$name};
    }


}