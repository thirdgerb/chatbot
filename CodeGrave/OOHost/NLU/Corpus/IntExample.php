<?php


namespace Commune\Chatbot\OOHost\NLU\Corpus;


/**
 * 符合NLU需求的训练样本.
 * 每个元素是一个样本. 用类似markdown link 的语法来标记entity.
 *
 * 例如 :
 * - "这句话的Entity就在[句尾](where)"
 * - "[北京](city)[明天](when)的天气怎么样?"
 * - "我想要来一杯[可乐](production)"
 *
 * @property-read string $example  带有Entity标记的样本文本.
 * @property-read string $text 去掉了Entity标记的原始文本.
 * @property-read ExampleEntity[] $entities
 */
class IntExample
{
    /**
     * @var string
     */
    protected $text = '';

    /**
     * @var ExampleEntity[]
     */
    protected $entities = [];

    /**
     * @var string
     */
    protected $example;

    /**
     * @var null|ExampleEntity
     */
    protected $lastEntity;

    /**
     * NLUExample constructor.
     * @param string $example
     */
    public function __construct($example)
    {
        $this->example = $example;
        $this->generateEntity($example);
    }

    protected function addEntity(ExampleEntity $entity) : void
    {
        if (isset($this->lastEntity)) {
            $this->lastEntity->next = $entity;
            $this->lastEntity->right = $entity->left;
        }
        $this->lastEntity = $entity;
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

        $this->text .= $leftPart = mb_substr($text, 0, $lsb);
        $this->text .= $value = mb_substr(
            $text,
            $lsb + 1,
            $width = $rsb - $lsb -1
        );

        $name = mb_substr($text, $lb + 1, $rb - $lb - 1);

        $rightPart = mb_substr($text, $rb + 1) ?? '';
        $this->addEntity(new ExampleEntity(
            $name,
            $leftPart,
            $value,
            $rightPart,
            $start + $lsb,
            mb_strlen($value)
        ));

        $this->generateEntity(
            $rightPart,
            mb_strlen($this->text)
        );
    }


    /**
     * @return ExampleEntity[]
     */
    public function getExampleEntities() : array
    {
        return $this->entities;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function __sleep()
    {
        return ['example'];
    }

    public function __wakeup()
    {
        $this->generateEntity($this->example);
    }
}