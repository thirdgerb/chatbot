<?php


namespace Commune\Chatbot\App\Components\SimpleFileIntent;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Intent\IntentDefinitionImpl;
use Commune\Chatbot\OOHost\Context\Intent\IntentMatcherOption;

class SimpleFileIntDefinition extends IntentDefinitionImpl
{

    /**
     * @var FileIntOption
     */
    protected $fileIntOption;

    public function __construct(FileIntOption $option)
    {
        $this->fileIntOption = $option;
        parent::__construct(
            $option->name,
            SimpleFileInt::class,
            $option->desc,
            new IntentMatcherOption(),
            null
        );
    }

    public function getFileIntentOption() : FileIntOption
    {
        return $this->fileIntOption;
    }

    public function getDesc(): string
    {
        return $this->fileIntOption->desc;
    }

    /**
     * create a context
     * @param array $args
     * @return SimpleFileInt
     */
    public function newContext(...$args) : Context
    {
        return new SimpleFileInt($this->getName());
    }

}