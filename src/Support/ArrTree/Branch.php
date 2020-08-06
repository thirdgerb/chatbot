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
     * @var string
     */
    protected $appending;

    /**
     * Branch constructor.
     * @param Tree $tree
     * @param string $name
     * @param Branch|null $parent
     * @param string $appending
     */
    public function __construct(
        Tree $tree,
        string $name,
        Branch $parent = null,
        string $appending = ''
    )
    {
        $this->tree = $tree;
        $this->parent = $parent;
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

        $this->appending = $appending;
        if (array_key_exists($this->name, $this->tree->branches)) {
            throw new \InvalidArgumentException(
                "duplicated branch name "
                . $this->name
            );
        }

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

            $self = new Branch($this->tree, $name, $this, $this->appending);
            if (isset($current)) {
                $current->younger = $self;
                $self->elder = $current;
            }
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

    public function __destruct()
    {
        unset(
            $this->parent,
            $this->younger,
            $this->elder,
            $this->children
        );
    }

}