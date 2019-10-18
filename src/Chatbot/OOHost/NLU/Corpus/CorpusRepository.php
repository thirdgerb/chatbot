<?php


namespace Commune\Chatbot\OOHost\NLU\Corpus;


use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\Manager;
use Commune\Chatbot\OOHost\NLU\Managers\CommonManager;
use Commune\Chatbot\OOHost\NLU\Managers\IntentManager;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\SynonymOption;
use Commune\Support\OptionRepo\Contracts\OptionRepository;

class CorpusRepository implements Corpus
{

    /**
     * @var IntentRegistrar
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
     * @param IntentRegistrar $registrar
     * @param OptionRepository $optionRepo
     */
    public function __construct(IntentRegistrar $registrar, OptionRepository $optionRepo)
    {
        $this->registrar = $registrar;
        $this->optionRepo = $optionRepo;
    }

    public function sync(bool $force = false): string
    {
        $this->intentCorpusManager()->sync($force);
        $this->entityDictManager()->sync($force);
        $this->synonymsManager()->sync($force);
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


}