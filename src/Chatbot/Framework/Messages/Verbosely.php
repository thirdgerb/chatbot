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
    protected $level = VerboseMsg::INFO;

    /**
     * @var array
     */
    protected $slots = [];

    /**
     * @var bool
     */
    protected $translate = true;


    /**
     * @var string
     */
    protected $translation;

    /**
     * @return $this
     */
    public function raw()
    {
        $this->translate = false;
        $this->translation = $this->input;
        return $this;
    }

    /**
     * @param array $slots
     * @return $this
     */
    public function withSlots(array $slots)
    {
        $this->slots = $slots + $this->slots;
        return $this;
    }

    /**
     * @param string $level
     * @return $this
     */
    public function withLevel(string $level)
    {
        $this->level = $level;
        return $this;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getSlots() : array
    {
        $result = [];
        foreach($this->slots as $key => $value) {

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
        if ($this->translate) {
            $this->translation = $this->doTranslate($translator, $locale);
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