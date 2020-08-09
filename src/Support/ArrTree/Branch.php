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
     * @var string|null
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
    public $order;

    /**
     * @var string
     */
    public $orderId;

    /**
     * @var string
     */
    protected $appending;

    protected $orderSeparator;

    /**
     * Branch constructor.
     * @param Tree $tree
     * @param string $name
     * @param Branch|null $parent
     * @param Branch|null $elder
     * @param string $appending
     * @param string $orderSeparator
     */
    public function __construct(
        Tree $tree,
        string $name,
        Branch $parent = null,
        Branch $elder = null,
        string $appending = '',
        string $orderSeparator = '_'
    )
    {
        $this->tree = $tree;
        $this->orderSeparator = $orderSeparator;
        $this->parent = $parent;
        $this->appending = $appending;

        if (isset($this->parent)) {
            $this->parent->children[] = $this;
        }

        if (!empty($appending)) {

            $prefix = $this->parent
                ? $this->parent->name . $appending
                : '';
            $name = $prefix.$name;
        }

        $this->name = $name;
        if (array_key_exists($this->name, $this->tree->branches)) {
            throw new \InvalidArgumentException(
                "duplicated branch name "
                . $this->name
            );
        }

        if (isset($elder)) {
            $elder->younger = $this;
            $this->elder = $elder;
        }

        $this->order = isset($elder)
            ? $elder->order + 1
            : 0;

        $this->orderId = isset($this->parent)
            ? ($this->parent->orderId . $this->orderSeparator . $this->order)
            : $this->name;

        $this->tree->branches[$name] = $this;
    }

    public function father(array $children) : void
    {
        /**
         * @var Branch|null $current
         */
        $current = null;
        foreach ($children as $index => $value) {
            if (is_string($index) && is_array($value)) {
                $name = $index;
                $grandChildren = $value;
            } elseif (is_int($index) && is_string($value)) {
                $name = $value;
                $grandChildren = [];
            } else {
                throw new \InvalidArgumentException(
                    'children array is invalid:'
                    . json_encode($children)
                );
            }

            $self = new Branch($this->tree, $name, $this, $current, $this->appending);

            $current = $self;
            if (!empty($grandChildren)) {
                $current->father($grandChildren);
            }
        }
    }


    public function toArray() : array
    {
        return [
            'name' => $this->name,
            'parent' => $this->parent ? $this->parent->name : null,
            'elder' => $this->elder ? $this->elder->name : null,
            'younger' => $this->younger ? $this->younger->name : null,
            'children' => array_map(function(Branch $child) {
                return $child->name;
            }, $this->children),
        ];
    }

    public function toTreeArr() : array
    {
        $data = [];
        if (empty($this->children)) {
            return $data;
        }

        foreach ($this->children as $child) {

            $name = $child->name;
            $childArr = $child->toTreeArr();
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