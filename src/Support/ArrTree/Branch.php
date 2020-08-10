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
class Branch implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    /**
     * @var Tree
     */
    public $tree;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Branch|null
     */
    public $parent;

    /**
     * @var Branch[]
     */
    public $children = [];

    /**
     * @var Branch|null
     */
    public $elder;

    /**
     * @var Branch|null
     */
    public $younger;

    /**
     * @var int
     */
    public $order = 0;


    /**
     * @var int
     */
    public $depth = 0;


    /**
     * @var string
     */
    public $orderId = 'root';

    public $orderSeparator = '_';

    /**
     * Branch constructor.
     * @param Tree $tree
     * @param string $name
     */
    public function __construct(Tree $tree, string $name)
    {
        $this->tree = $tree;
        $this->name = $name;
    }

    public function getFamilyName(string $join) : string
    {
        $name = $this->name;
        $parent = $this->parent;

        while (isset($parent)) {
            $name = $parent->name . $join . $name;
            $parent = $parent->parent;
        };

        return $name;
    }

    public function father(string $name) : Branch
    {
        $child = new Branch($this->tree, $name);
        $child->parent = $this;
        $child->depth = $this->depth + 1;

        $elder = array_pop($this->children);

        if (isset($elder)) {
            $child->order = $elder->order + 1;
            $elder->younger = $child;
            $child->elder = $elder;
            array_push($this->children, $elder);
        }

        if (empty($name)) {
            $name = (string) $child->order;
        }
        $child->name = $name;

        $child->orderId = $this->orderId . $this->orderSeparator . $child->order;


        if (array_key_exists($child->orderId, $this->tree->branches)) {
            throw new \InvalidArgumentException(
                "duplicated branch order id "
                . $child->orderId
            );
        }

        $this->tree->branches[$child->orderId] = $child;

        // 户籍都搞定了再加到族谱里.
        array_push($this->children, $child);
        return $child;
    }

    public function build(array $children) : void
    {
        /**
         * @var Branch|null $current
         */
        $current = null;

        foreach ($children as $index => $value) {

            // 有子孙时
            if (is_string($index) && is_array($value)) {
                $name = $index;
                $grandChildren = $value;


            // 没有子孙时.
            } elseif (is_int($index) && is_string($value)) {
                $name = $value;
                $grandChildren = [];
            } else {
                throw new \InvalidArgumentException(
                    'children array is invalid:'
                    . json_encode($children)
                );
            }

            $current = $this->father($name);

            if (!empty($grandChildren)) {
                $current->build($grandChildren);
            }
        }
    }


    public function toArray() : array
    {
        return [
            'orderId' => $this->orderId,

            'name' => $this->name,

            'parent' => isset($this->parent)
                ? $this->parent->name
                : null,

            'elder' => $this->elder ? $this->elder->name : null,

            'younger' => $this->younger ? $this->younger->name : null,

            'children' => array_map(function(Branch $child) {
                return $child->name;
            }, $this->children),

            'depth' => $this->depth,
        ];
    }

    public function toNameArr() : array
    {
        $data = [];
        if (empty($this->children)) {
            return $data;
        }

        foreach ($this->children as $child) {

            $name = $child->name;
            $childArr = $child->toNameArr();
            if (empty($childArr)) {
                $data[] = $name;
            } else {
                $data[$name] = $childArr;
            }
        }

        return $data;
    }

    public function toOrderArr()
    {
        $data = [];
        if (empty($this->children)) {
            return $data;
        }

        foreach ($this->children as $child) {

            $name = $child->orderId;
            $childArr = $child->toOrderArr();
            if (empty($childArr)) {
                $data[] = $name;
            } else {
                $data[$name] = $childArr;
            }
        }

        return $data;

    }

    public function destroy() : void
    {
        unset(
            $this->parent,
            $this->younger,
            $this->elder,
            $this->children
        );
    }

    public function __destruct()
    {
        $this->destroy();
    }

}