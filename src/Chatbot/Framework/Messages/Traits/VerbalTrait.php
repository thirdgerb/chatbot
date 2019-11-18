<?php


namespace Commune\Chatbot\Framework\Messages\Traits;

use Commune\Chatbot\Blueprint\Message\VerbalMsg;

/**
 * implements VerboseMsg
 */
trait VerbalTrait
{
    /**
     * @var int
     */
    protected $_level = VerbalMsg::INFO;

    /**
     * @param string $level
     * @return $this
     */
    public function withLevel(string $level)
    {
        $this->_level = $level;
        return $this;
    }

    public function getLevel(): string
    {
        return $this->_level;
    }

}