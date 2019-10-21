<?php

namespace Commune\Chatbot\OOHost\NLU\Corpus;

use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\Manager;
use Commune\Chatbot\OOHost\NLU\Managers\CommonManager;
use Commune\Chatbot\OOHost\NLU\Managers\IntentManager;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Chatbot\OOHost\NLU\Options\SynonymOption;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;

class CorpusRepository implements Corpus
{

    /**
     * @var RootIntentRegistrar
     */
    protected $registrar;

    /**
     * @var OptionRepository
     */
    protected $optionRepo;

    protected $corpusM;

    protected $entityM;

    protected $synonymsM;

    /**
     * CorpusRepository constructor.
     * @param RootIntentRegistrar $registrar
     * @param OptionRepository $optionRepo
     */
    public function __construct(RootIntentRegistrar $registrar, OptionRepository $optionRepo)
    {
        $this->registrar = $registrar;
        $this->optionRepo = $optionRepo;
    }

    public function sync(bool $force = false): string
    {
        $output = '';
        $result = $this->intentCorpusManager()->sync($force);
        $output .= empty($result) ? '' : "$result\n";
        $result = $this->entityDictManager()->sync($force);
        $output .= empty($result) ? '' : "$result\n";
        $result = $this->synonymsManager()->sync($force);
        $output .= empty($result) ? '' : "$result\n";

        return $output;
    }

    public function intentCorpusManager(): Manager
    {
        return $this->corpusM
            ?? $this->corpusM = new IntentManager(
                $this->registrar,
                $this->optionRepo
            );
    }

    public function entityDictManager(): Manager
    {
        return $this->entityM
            ?? $this->entityM = new CommonManager(
                $this->optionRepo,
                EntityDictOption::class
            );
    }

    public function synonymsManager(): Manager
    {
        return $this->synonymsM
            ?? $this->synonymsM = new CommonManager(
                $this->optionRepo,
                SynonymOption::class
            );
    }

    public function getManager(string $corpusOptionName): ? Manager
    {

        switch ($corpusOptionName) {
            case IntentCorpusOption::class :
                return $this->intentCorpusManager();
            case EntityDictOption::class :
                return $this->entityDictManager();
            case SynonymOption::class :
                return $this->synonymsManager();
            default :
                return null;
        }
    }


}