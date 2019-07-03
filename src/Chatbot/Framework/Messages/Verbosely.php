<?php


namespace Commune\Chatbot\Framework\Messages;

use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Contracts\Translator;

/**
 * @method string getInput() : string
 */
trait Verbosely
{

    /**
     * @var int
     */
    protected $_level = VerboseMsg::INFO;

    /**
     * @var array
     */
    protected $_slots = [];

    /**
     * @var bool
     */
    protected $_shouldTranslate = true;


    /**
     * @var string
     */
    protected $_translation;

    /**
     * @return $this
     */
    public function raw()
    {
        $this->_shouldTranslate = false;
        $this->_translation = $this->input;
        return $this;
    }

    /**
     * @param array $slots
     * @return $this
     */
    public function withSlots(array $slots)
    {
        $this->_slots = $slots + $this->_slots;
        return $this;
    }

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

    public function getSlots() : array
    {
        $result = [];
        foreach($this->_slots as $key => $value) {

            if (is_array($value)) {
                continue;
            }

            if (is_object($value) && !method_exists($value, '__toString')) {
                continue;
            }

            $result[$key] = (string) $value;
        }
        return $result;
    }

    public function translate(Translator $translator, string $locale = null): void
    {
        if ($this->_shouldTranslate) {
            $this->_translation = $this->doTranslate($translator, $locale);
        }
    }

    protected function doTranslate(Translator $translator, string $locale = null) : string
    {
        return $translator->trans(
            $this->getInput(),
            $this->getSlots(),
            Translator::MESSAGE_DOMAIN,
            $locale
        );
    }



}