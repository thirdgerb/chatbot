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

    protected function wrapNewOption(string $id): Option
    {

        $def = $this->registrar->getDef($id);
        if ($def instanceof IntentDefHasCorpus) {
            return $def->getDefaultCorpus();
        }

        $option = IntentCorpusOption::createById($id);
        $option->mergeEntityNames($def->getEntityNames());
        $option->setDesc($def->getDesc(), false);
        $keywords = $def->getMatcherOption()->keywords;
        if (!empty($keywords)) {
            $option->setKeywords($keywords);
        }

        return $option;
    }

    public function has(string $id): bool
    {
        return isset($this->loaded[$id]) || $this->registrar->hasDef($id);
    }

    public function each(): \Generator
    {
        foreach ($this->registrar->eachDef() as $def) {
            yield $this->get($def->getName());
        }
    }

    public function sync(bool $force = false): string
    {
        $this->flush();
        return parent::sync($force);
    }

    public function getAllIds(): array
    {
        return $this->registrar->getDefNamesByDomain('');
    }

}