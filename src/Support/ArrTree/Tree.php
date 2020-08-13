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
     * @var Branch
     */
    public $root;

    private function __construct()
    {
    }

    public static function build(array $tree, string $rootName, string $orderSeparator = '_') : Tree
    {
        $self = new static;
        $root = new Branch($self, $rootName);
        $root->orderSeparator = $orderSeparator;

        $self->root = $root;
        $self->branches[$root->orderId] = $root;

        $root->build($tree);
        return $self;
    }

    public function toArray() : array
    {
        $arr = [];
        foreach ($this->branches as $branch) {
            $arr[$branch->name] = $branch->toArray();
        }
        return $arr;
    }

    public function toNameArr() : array
    {
        $tree = [];
        $root = $this->root;
        $tree[$root->name] = $root->toNameArr();
        return $tree;
    }

    public function toOrderArr() : array
    {
        $tree = [];
        $root = $this->root;
        $tree[$root->name] = $root->toOrderArr();
        return $tree;
    }

    public function getBranchNames() : array
    {
        return array_values(
            array_map(
                function(Branch $branch) {
                    return $branch->name;
                },
                $this->branches
            )
        );
    }


    public function getBranchFamilyNames(string $join) : array
    {
        return array_values(
            array_map(
                function(Branch $branch) use ($join){
                    return $branch->getFamilyName($join);
                },
                $this->branches
            )
        );
    }

    public function getBranchMapByName() : array
    {
        $branches = [];
        foreach ($this->branches as $branch) {
            $branches[$branch->name] = $branch;
        }

        return $branches;
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

    /**
     * @return Branch[]
     */
    public function getBranchMapByOrderId() : array
    {
        return $this->branches;
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