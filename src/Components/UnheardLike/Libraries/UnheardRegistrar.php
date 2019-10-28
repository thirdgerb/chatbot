<?php


namespace Commune\Components\UnheardLike\Libraries;


use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Components\UnheardLike\Options\Episode;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Support\Utils\StringUtils;
use Illuminate\Support\Str;

class UnheardRegistrar implements ContextRegistrar
{
    /**
     * @var OptionRepository
     */
    protected $repo;

    protected $definitions = [];

    /**
     * UnheardRegistrar constructor.
     * @param OptionRepository $repo
     */
    public function __construct(OptionRepository $repo)
    {
        $this->repo = $repo;
    }


    public function getRegistrarId(): string
    {
        return static::class;
    }

    public function registerDef(Definition $def, bool $force = false): bool
    {
        return false;
    }

    public function hasDef(string $contextName): bool
    {
        $contextName = StringUtils::normalizeContextName($contextName);
        return $this->repo->has(Episode::class, $contextName);
    }

    public function getDef(string $contextName): ? Definition
    {
        if (!$this->hasDef($contextName)) {
            return null;
        }
        $contextName = StringUtils::normalizeContextName($contextName);

        return $this->definitions[$contextName]
            ?? $this->definitions[$contextName] = new EpisodeDefinition(
                $this->repo->find(Episode::class, $contextName)
            );
    }

    public static function validateDefName(string $contextName): bool
    {
        return StringUtils::validateDefName($contextName);
    }

    public function eachDef(): \Generator
    {
        $ids = $this->repo->getAllOptionIds(Episode::class);
        foreach ($ids as $id) {
            yield $this->getDef($id);
        }
    }

    public function countDef(): int
    {
        return $this->repo->count(Episode::class);
    }

    public function getDefNamesByDomain(string $domain = ''): array
    {
        $ids = $this->repo->getAllOptionIds(Episode::class);
        $result = [];
        foreach ($ids as $id) {
            if (Str::startsWith($id, $domain)) {
                $result[] = $id;
            }
        }

        return $result;
    }

    public function getDefNamesByTag(string ...$tags): array
    {
        if (in_array(Definition::TAG_CONFIGURE, $tags)) {
            return $this->repo->getAllOptionIds(Episode::class);
        }
        return [];
    }

    public function getPlaceholderDefNames(): array
    {
        return [];
    }


}