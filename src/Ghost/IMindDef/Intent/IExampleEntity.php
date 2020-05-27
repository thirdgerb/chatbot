<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindDef\Intent;

use Commune\Blueprint\Ghost\MindDef\Intent\ExampleEntity;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IExampleEntity implements ExampleEntity
{
    /**
     * @var ExampleEntity|null
     */
    public $next;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;

    /**
     * @var int
     */
    public $start;

    /**
     * @var int
     */
    public $width;

    /**
     * @var string
     */
    public $right = '';


    /**
     * @var string
     */
    public $left = '';

    /**
     * ExampleEntity constructor.
     * @param string $entityName
     * @param string $left
     * @param string $value
     * @param string $right
     * @param int $start
     * @param int $width
     */
    public function __construct(
        string $entityName,
        string $left,
        string $value,
        string $right,
        int $start,
        int $width
    )
    {
        $this->name = $entityName;
        $this->left = $left;
        $this->value = $value;
        $this->right = $right;
        $this->start = $start;
        $this->width = $width;
    }



}