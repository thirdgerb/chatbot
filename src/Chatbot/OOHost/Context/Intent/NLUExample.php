<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


/**
 * @property-read string $text
 * @property-read string $intentName
 * @property-read string $originText
 * @property-read NLUExampleEntity[] $entities
 */
class NLUExample
{
    /**
     * @var string
     */
    protected $intentName;

    /**
     * @var string
     */
    protected $text = '';

    /**
     * @var NLUExampleEntity[]
     */
    protected $entities = [];

    /**
     * @var string
     */
    protected $originText;

    /**
     * NLUExample constructor.
     * @param string $intentName
     * @param string $originText
     * @param NLUExampleEntity[] $entities
     */
    public function __construct(
        string $intentName,
        string $originText,
        array $entities = []
    )
    {
        $this->originText = $originText;
        $this->intentName = $intentName;
        foreach ($entities as $entity) {
            $this->addEntity($entity);
        }
        $this->generateEntity($originText);
    }

    protected function addEntity(NLUExampleEntity $entity) : void
    {
        $this->entities[] = $entity;
    }

    protected function generateEntity(string $text, $start = 0) : void
    {
        if (empty(mb_strlen($text))) {
            return ;
        }
        $lsb = mb_strpos($text, '[');
        $rsb = mb_strpos($text, ']');
        $lb = mb_strpos($text, '(');
        $rb = mb_strpos($text, ')');

        $hasPos = is_int($lsb) && is_int($rsb) && is_int($lb) && is_int($rb);
        if (! $hasPos) {
            $this->text .= $text;
            return;
        }

        $rightOrder = ($rb > ($lb + 1))
            && ($lb === ($rsb + 1))
            && ($rsb > ($lsb + 1));

        if (!$rightOrder) {
            $this->text .= $text;
            return;
        }

        $this->text .= mb_substr($text, 0, $lsb);
        $this->text .= $value = mb_substr(
            $text,
            $lsb + 1,
            $width = $rsb - $lsb -1
        );

        $name = mb_substr($text, $lb + 1, $rb - $lb - 1);

        $this->addEntity(new NLUExampleEntity(
            $name,
            $value,
            $start + $lsb,
            mb_strlen($value)
        ));

        $this->generateEntity(
            mb_substr($text, $rb + 1) ?? '',
            mb_strlen($this->text)
        );
    }


    /**
     * @return NLUExampleEntity[]
     */
    public function getExampleEntities() : array
    {
        return $this->entities;
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}