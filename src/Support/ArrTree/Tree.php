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

    /**
     * @var Branch[]
     */
    public $roots = [];

    public function build(array $tree, string $rootName, string $appending = '') : Branch
    {
        $root = new Branch($this, $rootName, null, null, $appending);
        $root->father($tree);
        $this->roots[$root->name] = $root;

        return $root;
    }

    public function toArray() : array
    {
        return array_map(function(Branch $branch) {
            return $branch->toArray();
        }, $this->branches);
    }

    public function toTreeArr() : array
    {
        $tree = [];
        foreach($this->roots as $root) {
            $tree[$root->name] = $root->toTreeArr();
        }
        return $tree;
    }

    public function toOrderArr() : array
    {
        $tree = [];
        foreach($this->roots as $root) {
            $tree[$root->name] = $root->toOrderArr();
        }
        return $tree;
    }

    public function getBranchNames() : array
    {
        return array_keys($this->branches);
    }

    public function getBranchOrders() : array
    {
        return array_values(
            array_map(
                function(Branch $branch) {
                    return $branch->orderId;
                },
                $this->branches
            )
        );
    }

    public function destroy() : void
    {
        foreach ($this->branches as $branch) {
            $branch->destroy();
        }
        $this->__destruct();
    }

    public function __destruct()
    {
        unset(
            $this->branches,
            $this->roots
        );
    }
}