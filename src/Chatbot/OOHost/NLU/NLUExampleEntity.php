<?php


namespace Commune\Chatbot\OOHost\NLU;


class NLUExampleEntity
{
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
     * NLUExampleEntity constructor.
     * @param string $name
     * @param string $value
     * @param int $start
     * @param int $width
     */
    public function __construct(string $name, string $value, int $start, int $width)
    {
        $this->name = $name;
        $this->value = $value;
        $this->start = $start;
        $this->width = $width;
    }


}