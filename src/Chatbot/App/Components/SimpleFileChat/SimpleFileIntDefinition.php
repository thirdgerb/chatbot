<?php


namespace Commune\Chatbot\App\Components\SimpleFileChat;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Intent\IntentDefinitionImpl;
use Commune\Chatbot\OOHost\Context\Intent\IntentMatcherOption;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;

class SimpleFileIntDefinition extends IntentDefinitionImpl
{

    /**
     * @var FileChatConfig
     */
    protected $fileChatConfig;

    public function __construct(FileChatConfig $option)
    {
        $this->fileChatConfig = $option;
        parent::__construct(
            $option->name,
            SimpleFileInt::class,
            $option->desc,
            new IntentMatcherOption(),
            null
        );
    }

    protected function initialize(): void
    {
        parent::initialize();

        $keys = array_keys($this->fileChatConfig->contents);

        $caller = function(Stage $stage) : Navigator  {
            $stageName = $stage->name;
            $method = SimpleFileInt::STAGE_METHOD_PREFIX . $stageName;

            return call_user_func(
                [$stage->self, $method],
                $stage
            );
        };

        foreach($keys as $key) {
            $stageName = SimpleFileInt::STAGE_PREFIX . $key;
            $this->setStage($stageName, $caller);
        }
    }

    public function getFileChatConfig() : FileChatConfig
    {
        return $this->fileChatConfig;
    }

    public function getDesc(): string
    {
        return $this->fileChatConfig->desc;
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