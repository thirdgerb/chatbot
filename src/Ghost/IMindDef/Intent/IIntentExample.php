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
use Commune\Blueprint\Ghost\MindDef\Intent\IntentExample;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IIntentExample implements IntentExample
{
    /**
     * @var string
     */
    protected $text = '';

    /**
     * @var IExampleEntity[]
     */
    protected $entities = [];

    /**
     * @var string
     */
    protected $example;

    /**
     * @var null|IExampleEntity
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
        $this->addEntity(new IExampleEntity(
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

    public function getOrigin(): string
    {
        return $this->example;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getEntities(): array
    {
        return $this->entities;
    }


    public function __sleep()
    {
        return ['example'];
    }

    public function __wakeup()
    {
        $this->generateEntity($this->example);
    }

    public function __destruct()
    {
        $this->entities = [];
    }

}