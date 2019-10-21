<?php


namespace Commune\Chatbot\OOHost\NLU\Managers;


use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;
use Commune\Chatbot\OOHost\NLU\Contracts\IntentDefHasCorpus;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Support\Option;
use Commune\Support\OptionRepo\Contracts\OptionRepository;

class IntentManager extends CommonManager
{
    /**
     * @var RootIntentRegistrar
     */
    protected $registrar;

    public function __construct(RootIntentRegistrar $registrar, OptionRepository $optionRepo )
    {
        $this->registrar = $registrar;
        parent::__construct($optionRepo, IntentCorpusOption::class);
    }

    public function count(): int
    {
        return $this->registrar->countDef();
    }

    /**
     * @param IntentCorpusOption $option
     * @param bool $isNew
     * @return Option
     */
    public function wrapNewOption(Option $option, bool $isNew): Option
    {
        $intentName = $option->name;
        if ($this->registrar->hasDef($intentName)) {
            $def = $this->registrar->getDef($intentName);
            if ($isNew && $def instanceof IntentDefHasCorpus) {
                return $def->getDefaultCorpus();
            }

            $option->mergeEntityNames($def->getEntityNames());
            $option->setDesc($def->getDesc(), false);
        }
        return $option;
    }

    public function each(): \Generator
    {
        foreach ($this->registrar->eachDef() as $def) {
            yield $this->get($def->getName());
        }
    }


}