<?php


namespace Commune\Chatbot\OOHost\NLU\Corpus;


use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Psr\Container\ContainerInterface;

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

    /**
     * @var IntentCorpusOption[]
     */
    protected $loadedIntents = [];

    /**
     * @var EntityDictOption[]
     */
    protected $loadedEntities = [];

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


    public function sync(): void
    {
        $toSave = [];
        foreach ($this->eachIntentCorpus() as $option) {
            $id = $option->getId();

            // 如果没有存储. 主动存储
            if (!$this->optionRepo->has( IntentCorpusOption::class, $id)) {
                $toSave[] = $option;

            // 如果有存储, 也要比较一下 hash
            } else {
                $saved = $this->optionRepo->find( IntentCorpusOption::class, $id);

                if ($saved->getHash() != $option->getHash()) {
                    $toSave[] = $option;
                }
            }

        }

        if (!empty($toSave)) {
            $this->optionRepo->saveBatch( IntentCorpusOption::class, true, ...$toSave);
        }

    }

    public function hasIntentCorpus(string $intentName): bool
    {
        if (empty($intentName)) {
            return false;
        }

        return isset($this->loadedIntents[$intentName]) || $this->optionRepo->has( IntentCorpusOption::class, $intentName);
    }

    public function getIntentCorpus(string $intentName): IntentCorpusOption
    {
        if (isset($this->loadedIntents[$intentName])) {
            return $this->loadedIntents[$intentName];
        }

        $has = $this->optionRepo
            ->has(

                IntentCorpusOption::class,
                $intentName
            );

        if ($has) {
            $option = $this->optionRepo
                ->find(

                    IntentCorpusOption::class,
                    $intentName
                );

        } else {

            $option = new IntentCorpusOption([
                'intentName' => $intentName
            ]);
        }

        if ($this->registrar->hasDef($intentName)) {
            $def = $this->registrar->getDef($intentName);
            $option->mergeEntityNames($def->getEntityNames());
            $option->setDesc($def->getDesc(), false);
        }

        return $this->loadedIntents[$intentName] = $option;
    }

    public function removeIntentCorpus(string $intentName): void
    {
        unset($this->loadedIntents[$intentName]);
        $this->optionRepo->delete( IntentCorpusOption::class, $intentName);
    }


    /**
     * @return IntentCorpusOption[]
     */
    public function eachIntentCorpus(): \Generator
    {
        foreach ($this->registrar->getDefNamesByDomain('') as $name) {
            yield $this->getIntentCorpus($name);
        }
    }

    public function getIntentCorpusMap(array $intentNames): array
    {
        $options = [];
        foreach ($intentNames as $name) {
            $options[$name] = $this->getIntentCorpus($name);
        }
        return $options;
    }


    public function hasEntityDict(string $EntityName): bool
    {
        return isset($this->loadedEntities[$EntityName]) || $this->optionRepo->has( EntityDictOption::class, $EntityName);
    }


    public function getEntityDict(string $EntityName): EntityDictOption
    {
        if (isset($this->loadedEntities[$EntityName])) {
            return $this->loadedEntities[$EntityName];
        }

        $has = $this->optionRepo
            ->has(

                EntityDictOption::class,
                $EntityName
            );

        if ($has) {
            $option = $this->optionRepo
                ->find(

                    EntityDictOption::class,
                    $EntityName
                );

        } else {

            $option = new EntityDictOption([
                'name' => $EntityName
            ]);
        }

        return $this->loadedEntities[$EntityName] = $option;
    }

    public function removeEntityDict(string $EntityName): void
    {
        unset($this->loadedEntities[$EntityName]);
        $this->optionRepo->delete( EntityDictOption::class, $EntityName);
    }


    /**
     * @return EntityDictOption[]
     */
    public function eachEntityDict(): \Generator
    {
        foreach ($this->registrar->getDefNamesByDomain('') as $name) {
            yield $this->getEntityDict($name);
        }
    }

    public function getEntityDictMap(array $EntityNames): array
    {
        $options = [];
        foreach ($EntityNames as $name) {
            $options[$name] = $this->getEntityDict($name);
        }
        return $options;
    }


}