<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Mind\Registries;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\Mind\Definitions\Def;
use Commune\Blueprint\Ghost\Mind\Metas\DefMeta;
use Commune\Blueprint\Ghost\Mind\Mindset;
use Commune\Blueprint\Ghost\Mind\Registries\DefRegistry;
use Commune\Support\Registry\Category;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsDefRegistry implements DefRegistry
{

    /**
     * @var Mindset
     */
    protected $mindset;

    /**
     * @var OptRegistry
     */
    protected $optRegistry;

    /**
     * @var int
     */
    protected $cacheExpire;

    /*------ cached ------*/

    /**
     * @var int
     */
    protected $expireAt = 0;

    /**
     * @var Def[]
     */
    protected $cachedDefs = [];

    /**
     * @var bool[]|null
     */
    protected $registeredIds = [];

    /**
     * AbsDefRegistry constructor.
     * @param Mindset $mindset
     * @param OptRegistry $optRegistry
     * @param int $cacheExpire
     */
    public function __construct(Mindset $mindset, OptRegistry $optRegistry, int $cacheExpire)
    {
        $this->mindset = $mindset;
        $this->optRegistry = $optRegistry;
        $this->cacheExpire = $cacheExpire;
    }

    /*------- meta -------*/

    abstract protected function getDefType() : string;

    protected function checkDefType(string $method, Def $def)  : void
    {
        $type = $this->getDefType();
        if (!is_a($def, $type, TRUE)) {
            throw new InvalidArgumentException(
                $method,
                'def',
                'def should be subclass of ' . $type
            );
        }
    }

    protected function hasRegisteredMeta(string $defName) : bool
    {
        return $this->getMetaRegistry()->has($defName);
    }

    protected function getRegisteredMeta(string $defName) : DefMeta
    {
        /**
         * @var DefMeta $meta
         */
        $meta = $this->getMetaRegistry()->find($defName);
        return $meta;
    }

    protected function doRegisterDef(Def $def, bool $notExists) : bool
    {
        $meta = $def->getMeta();
        return $this->getMetaRegistry()->save($meta, $notExists);
    }

    protected function getRegisteredMetaIds() : array
    {
        return $this->getMetaRegistry()->getAllIds();
    }

    /*------- implements -------*/

    public function getMetaRegistry(): Category
    {
        $metaId = $this->getMetaId();
        return $this->optRegistry->getCategory($metaId);
    }


    public function flushCache(): void
    {
        $this->cachedDefs = [];
        $this->registeredIds = null;
    }

    protected function checkExpire() : void
    {
        $now = time();
        if ($now > $this->expireAt) {
            $this->flushCache();
        }
        $this->expireAt = $now + $this->cacheExpire - ($now % $this->cacheExpire);
    }

    public function hasDef(string $defName): bool
    {
        $this->checkExpire();
        return array_key_exists($defName, $this->cachedDefs)
            || $this->hasRegisteredMeta($defName);
    }

    public function getDef(string $defName): Def
    {
        $this->checkExpire();
        if (isset($this->cachedDefs[$defName])) {
            return $this->cachedDefs[$defName];
        }

        if (!$this->hasRegisteredMeta($defName)) {
            throw new DefNotDefinedException(
                __METHOD__,
                $this->getMetaId(),
                $defName
            );
        }

        $meta = $this->getRegisteredMeta($defName);
        $def = $meta->getWrapper();
        return $this->cachedDefs[$defName] = $def;
    }

    public function getAllDefIds(): array
    {
        $this->prepareAllDefIds();
        return array_keys($this->registeredIds);
    }

    protected function prepareAllDefIds() : void
    {
        $this->checkExpire();
        if (!isset($this->registeredIds)) {
            $this->registeredIds = array_fill_keys($this->getRegisteredMetaIds(), true);
        }
    }

    public function searchIds(string $wildcardId): array
    {
        if (StringUtils::isWildCardPattern($wildcardId)) {
            $ids = $this->getAllDefIds();
            $pattern = StringUtils::wildcardToRegex($wildcardId);
            return empty($ids)
                ? []
                : array_filter($ids, function($id) use ($pattern){
                    return (bool) preg_match($pattern, $id);
                });
        }

        $this->prepareAllDefIds();
        return array_key_exists($wildcardId, $this->registeredIds)
            ? [$wildcardId]
            : [];
    }

    public function searchIdExists(string $wildcardId): int
    {
        $ids = $this->searchIds($wildcardId);
        return count($ids);
    }


    public function registerDef(Def $def, bool $notExists = true): bool
    {
        $this->checkExpire();
        $this->checkDefType(__METHOD__, $def);

        $name = $def->getName();
        if ($notExists && $this->hasDef($name)) {
            return false;
        }

        $this->cachedDefs[$name] = $def;
        return $this->doRegisterDef($def, $notExists);
    }

    public function each(): \Generator
    {
        $ids = $this->getAllDefIds();
        foreach ($ids as $id) {
            yield $this->getDef($id);
        }
    }

    public function paginate(int $offset = 0, int $limit = 20): array
    {
        $ids = $this->getAllDefIds();
        $ids = array_slice($ids, $offset, $limit);
        return array_map(function($id){
            return $this->getDef($id);
        }, $ids);
    }


}