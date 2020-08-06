<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\ArrTree;

use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Tree implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    /**
     * @var Branch[]
     */
    public $branches = [];

    public function build(array $tree, string $rootName, string $appending = '') : Branch
    {
        $root = new Branch($this, $rootName, null, $appending);
        $root->father($tree);
        return $root;
    }

    public function toArray() : array
    {
        return array_map(function(Branch $branch) {
            return $branch->toArray();
        }, $this->branches);
    }

    public function __destruct()
    {
        unset(
            $this->branches
        );
    }
}