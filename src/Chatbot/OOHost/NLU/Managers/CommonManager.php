<?php


namespace Commune\Chatbot\OOHost\NLU\Managers;


use Commune\Chatbot\OOHost\NLU\Contracts\Manager;
use Commune\Support\Option;
use Commune\Support\OptionRepo\Contracts\OptionRepository;

class CommonManager implements Manager
{
    /**
     * @var OptionRepository
     */
    protected $optionRepo;

    /**
     * @var string
     */
    protected $optionClass;


    /**
     * @var Option[]
     */
    protected $loaded = [];


    /**
     * @var Option[]
     */
    protected $toSave = [];

    /**
     * AbsManager constructor.
     * @param OptionRepository $optionRepo
     * @param string $optionClass
     */
    public function __construct(OptionRepository $optionRepo, string $optionClass)
    {
        $this->optionRepo = $optionRepo;
        $this->optionClass = $optionClass;
    }


    public function count(): int
    {
        return $this->optionRepo->count($this->optionClass) + count($this->toSave);
    }

    public function has(string $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return isset($this->loaded[$id])
            || $this->optionRepo->has( $this->optionClass, $id);
    }

    public function get(string $id): Option
    {
        if (isset($this->loaded[$id])) {
            return $this->loaded[$id];
        }
        $class = $this->optionClass;

        $has = $this->optionRepo->has($class, $id);

        if ($has) {
            $option = $this->optionRepo->find($class, $id);
        } else {
            $option = $this->wrapNewOption($id);
            $this->toSave[$option->getId()] = $option;
        }

        return $this->loaded[$id] = $option;
    }

    protected function wrapNewOption(string $id) : Option
    {
        return call_user_func([$this->optionClass, 'createById'], $id);
    }


    public function remove(string $id): void
    {
        unset($this->loaded[$id]);
        $this->optionRepo->delete( $this->optionClass, $id);
    }

    public function getMap(string ...$ids): array
    {
        $map = [];
        foreach ($ids as $id) {
            if ($this->has($id)) {
                $map[$id] = $this->get($id);
            }
        }
        return $map;
    }

    public function register(Option $option) : bool
    {
        if (!$this->has($option)) {
            $this->loaded[$option->getId()] = $option;
            $this->toSave[$option->getId()] = $option;
            return true;
        }
        return false;
    }

    public function save(Option $option): string
    {
        try {
            $this->loaded[$option->getId()] = $option;
            $this->optionRepo->save($this->optionClass, $option);
            return '';
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public function each(): \Generator
    {
        foreach ($this->optionRepo->eachOption($this->optionClass) as $option) {
            yield $option;
        }

        foreach ($this->toSave as $option) {
            yield $option;
        }

    }

    public function flush(): void
    {
        $ids = $this->optionRepo->getAllOptionIds($this->optionClass);
        $ids = array_values($ids);
        if (!empty($ids)) {
            $this->optionRepo->delete($this->optionClass, ...$ids);
        }
    }

    public function getAllIds(): array
    {
        return $this->optionRepo->getAllOptionIds($this->optionClass);
    }


    public function sync(bool $force = false): string
    {
        try {

            // 遍历一遍, 目的是有可能有要通过 get 来获取的.
            foreach ($this->each() as $option) {
                continue;
            }

            $toSave = [];
            foreach ($this->toSave as $option) {
                $id = $option->getId();

                // 如果没有存储. 主动存储
                if ($force || !$this->optionRepo->has( $this->optionClass, $id)) {
                    $toSave[] = $option;
                }
            }

            if (!empty($toSave)) {
                $this->optionRepo->saveBatch( $this->optionClass, true, ...$toSave);
            }

            return '';
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public function hasSynced(string $id): bool
    {
        return $this->optionRepo->has($this->optionClass, $id);
    }


}