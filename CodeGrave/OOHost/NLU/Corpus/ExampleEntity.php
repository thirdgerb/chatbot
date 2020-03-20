<?php


namespace Commune\Chatbot\OOHost\NLU\Corpus;


class ExampleEntity
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