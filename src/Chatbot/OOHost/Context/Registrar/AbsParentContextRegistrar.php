<?php


namespace Commune\Chatbot\OOHost\Context\Registrar;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\ParentContextRegistrar;
use Commune\Support\Utils\StringUtils;

abstract class AbsParentContextRegistrar implements ParentContextRegistrar
{

    /**
     * @var Application;
     */
    protected $chatApp;

    /**
     * @var string
     */
    protected $defaultId;

    /**
     * @var ContextRegistrar[]
     */
    protected $subRegistrars = [];

    /**
     * ParentContextRegistrarImpl constructor.
     * @param Application $chatApp
     * @param string $defaultId
     * @param ContextRegistrar|null $parent
     */
    public function __construct(
        Application $chatApp,
        string $defaultId
    )
    {
        $this->chatApp = $chatApp;
        $this->defaultId = $defaultId;
        $this->subRegistrars[$defaultId] = null;
    }

    public function getChatApp(): Application
    {
        return $this->chatApp;
    }

    public function getDefault(): ContextRegistrar
    {
        return $this->getSubRegistrar($this->defaultId, false);
    }


    public function hasSubRegistrar(string $registrarId): bool
    {
        if (isset($this->subRegistrars[$registrarId])) {
            return true;
        }

        foreach ($this->eachSubRegistrar(true) as $item) {
            if ($item->getRegistrarId() === $registrarId) {
                return true;
            }
        }

        return false;
    }

    public function registerSubRegistrar(string $id): void
    {
        $this->subRegistrars[$id] = null;
    }

    /**
     * @param bool $recursive
     * @return ContextRegistrar[]
     */
    public function eachSubRegistrar($recursive = true): \Generator
    {
        foreach ($this->subRegistrars as $id => $value) {
            $reg = $this->getSubRegistrar($id, false);
            yield $reg;

            if ($recursive && $reg instanceof ParentContextRegistrar) {
               foreach ($reg->eachSubRegistrar($recursive) as $item) {
                   yield $item;
               }
            }
        }
    }

    public function getSubRegistrar(string $id, bool $recursive = false): ? ContextRegistrar
    {
        if ($recursive) {
            foreach ($this->eachSubRegistrar(true) as $item) {
                if ($item->getRegistrarId() === $recursive) {
                    return $item;
                }
            }
            return null;
        }

        if (!array_key_exists($id, $this->subRegistrars)) {
            return null;
        }

        $reg = $this->subRegistrars[$id];

        if (isset($reg)) {
            return $reg;
        }

        $reg = $this->getChatApp()->getProcessContainer()->make($id);
        return $this->subRegistrars[$id] = $reg;
    }

    public function registerDef(Definition $def, bool $force = false): bool
    {
        return $this->getDefault()->registerDef($def, $force);
    }

    public function hasDef(string $contextName): bool
    {
        foreach ($this->eachSubRegistrar(false) as $item) {
            if ($item->hasDef($contextName)) {
                return true;
            }
        }

        return false;
    }

    public function getDef(string $contextName): ? Definition
    {
        foreach ($this->eachSubRegistrar(false) as $item) {
            $value = $item->getDef($contextName);
            if (isset($value)) {
                return $value;
            }
        }

        return null;
    }

    final public static function validateDefName(string $contextName): bool
    {
        return StringUtils::validateDefName($contextName);
    }

    public function eachDef(): \Generator
    {
        foreach ($this->eachSubRegistrar(false) as $item) {
            foreach ($item->eachDef() as $def) {
                yield $def;
            }
        }
    }

    public function countDef(): int
    {
        $sum = 0;
        foreach ($this->eachSubRegistrar(false) as $item) {
            $sum += $item->countDef();
        }
        return $sum;
    }

    public function getDefNamesByDomain(string $domain = ''): array
    {
        $names = [];
        foreach ($this->eachSubRegistrar(false) as $item) {
            $names = array_merge($names, $item->getDefNamesByDomain($domain));
        }
        return $names;
    }

    public function getDefNamesByTag(string ...$tags): array
    {
        $names = [];
        foreach ($this->eachSubRegistrar(false) as $item) {
            $names = array_merge($names, $item->getDefNamesByTag(...$tags));
        }
        return $names;
    }

    public function getPlaceholderDefNames(): array
    {
        $names = [];
        foreach ($this->eachSubRegistrar(false) as $item) {
            $names = array_merge($names, $item->getPlaceholderDefNames());
        }
        return $names;
    }

}