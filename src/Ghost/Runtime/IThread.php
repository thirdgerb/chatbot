<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;
use Commune\Ghost\Blueprint\Runtime\Thread;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IThread implements Thread
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $current;

    /**
     * @var Node[] string => Node
     */
    protected $nodes = [];

    protected $stacks = [];

    /**
     * @var int
     */
    protected $gcCount = 0;



    public function gc() : bool
    {
        $this->gcCount --;
        return $this->gcCount <= 0;
    }

    public function setGc(int $turns)
    {
        $this->gcCount = $turns;
    }


}