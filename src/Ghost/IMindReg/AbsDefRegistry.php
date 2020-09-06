<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindReg;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\MindDef\Def;
use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Blueprint\Ghost\MindReg\DefRegistry;
use Commune\Framework\Spy\SpyAgency;
use Commune\Support\Registry\Category;
use Commune\Support\Registry\Exceptions\OptionNotFoundException;
use Commune\Support\Registry\OptRegistry;


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
     * AbsDefRegistry constructor.
     * @param Mindset $mindset
     * @param OptRegistry $optRegistry
     * @param int $cacheExpire
     */
    public function __construct(Mindset $mindset, OptRegistry $optRegistry, int $cacheExpire)
    {
        $this->mindset = $mindset;
        $this->optRegistry = $optRegistry;
        $this->cacheExpire = $cacheExpire > 0 ? $cacheExpire : 0;
        SpyAgency::incr(self::class);
    }

    /*------- meta -------*/


    abstract protected function getDefType() : string;

    protected function checkDefType(string $method, Def $def)  : void
    {
        $type = $this->getDefType();
        if (!is_a($def, $type, TRUE)) {
            throw new InvalidArgumentException($method . ' def should be subclass of ' . $type);
        }
    }

    protected function hasRegisteredMeta(string $defName) : bool
    {
        return $this->getMetaRegistry()->has($defName);
    }

    protected function getRegisteredMeta(string $defName) : DefMeta
    {
        try {

            /**
             * @var DefMeta $meta
             */
            $meta = $this->getMetaRegistry()->find($defName);
            return $meta;

        } catch (OptionNotFoundException $e) {
            throw new DefNotDefinedException(
                $this->getMetaId(),
                $defName,
                $e
            );
        }
    }

    protected function doRegisterDef(Def $def, bool $notExists) : bool
    {
        $meta = $def->toMeta();
        return $this->getMetaRegistry()->save($meta, $notExists);
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
    }

    protected function checkExpire() : void
    {
        if ($this->cacheExpire <= 0) {
            return;
        }

        $now = time();
        if ($now > $this->expireAt) {
            $this->flushCache();
        }
        $this->expireAt = $now + $this->cacheExpire - ($now % $this->cacheExpire);
    }

    public function hasDef(string $defName): bool
    {
        $defName = $this->normalizeDefName($defName);

        $this->checkExpire();

        if (array_key_exists($defName, $this->cachedDefs)) {
            return true;
        }

        $has = $this->hasRegisteredMeta($defName);
        return $has;
    }

    abstract protected function normalizeDefName(string $name) : string;

    public function getDef(string $defName): Def
    {
        $defName = $this->normalizeDefName($defName);

        $this->checkExpire();

        // 有缓存
        if ($this->cacheExpire > 0 && isset($this->cachedDefs[$defName])) {
            return $this->cachedDefs[$defName];
        }

        if (!$this->hasDef($defName)) {
            throw new DefNotDefinedException(
                $this->getMetaId(),
                $defName
            );
        }

        $meta = $this->getRegisteredMeta($defName);
        $def = $meta->toWrapper();

        // 用 null 表示存在, 但不缓存.
        $this->setDefCache($defName, $def);
        return $def;
    }

    public function searchCount(string $query): int
    {
        return $this->getMetaRegistry()->searchCount($query);
    }

    public function searchIds(
        string $query,
        int $offset = 0,
        int $limit = 20
    ): array
    {
        return $this->getMetaRegistry()->searchIds(
            $query,
            $offset,
            $limit
        );
    }

    public function searchDefs(
        string $query,
        int $offset = 0,
        int $limit = 20
    ): array
    {
        return $this->getMetaRegistry()->search(
            $query,
            $offset,
            $limit
        );
    }

    public function paginateIds(int $offset = 0, int $limit = 20): array
    {
        return $this->getMetaRegistry()->paginateId($offset, $limit);
    }


    public function registerDef(Def $def, bool $notExists = true): bool
    {
        $this->checkExpire();
        $this->checkDefType(__METHOD__, $def);

        $name = $def->getName();

        if ($notExists && $this->getMetaRegistry()->has($name)) {
            return $this->alreadyHasDef($def);
        }

        $this->setDefCache($name, $def);
        return $this->doRegisterDef($def, $notExists);
    }

    protected function alreadyHasDef(Def $def) : bool
    {
        return false;
    }

    protected function setDefCache(string $name, Def $def) : void
    {
        if ($this->cacheExpire > 0) {
            $this->cachedDefs[$name] = $def;
        }
    }

    public function each(): \Generator
    {
        foreach($this->getMetaRegistry()->eachId() as $id ) {
            yield $this->getDef($id);
        }
    }

    public function paginate(int $offset = 0, int $limit = 20): array
    {
        $ids = $this->getMetaRegistry()->paginateId($offset, $limit);
        return array_map(function($id){
            return $this->getDef($id);
        }, $ids);
    }

    public function reset(): void
    {
        $category = $this->getMetaRegistry();
        $category->flush(false);
        $category->initialize();
    }


    public function __destruct()
    {
        unset($this->cachedDefs);
        unset($this->mindset);
        unset($this->optRegistry);
        SpyAgency::decr(self::class);
    }

}