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
use Commune\Support\Registry\Category;
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
        $now = time();
        if ($now > $this->expireAt) {
            $this->flushCache();
        }
        $this->expireAt = $now + $this->cacheExpire - ($now % $this->cacheExpire);
    }

    public function hasDef(string $defName): bool
    {
        $this->checkExpire();
        if (array_key_exists($defName, $this->cachedDefs)) {
            return true;
        }

        if ($this->hasRegisteredMeta($defName)) {
            /**
             * @var DefMeta $meta
             */
            $meta = $this->getMetaRegistry()->find($defName);
            $this->registerDef($meta->getWrapper());
            return true;
        }

        return false;
    }

    public function getDef(string $defName): Def
    {
        $this->checkExpire();

        // 没有缓存又没有注册
        if (
            !array_key_exists($defName, $this->cachedDefs)
            && !$this->hasRegisteredMeta($defName)
        ) {
            throw new DefNotDefinedException(
                __METHOD__,
                $this->getMetaId(),
                $defName
            );
        }

        // 有缓存
        if (isset($this->cachedDefs[$defName])) {
            return $this->cachedDefs[$defName];
        }

        $meta = $this->getRegisteredMeta($defName);
        $def = $meta->getWrapper();

        // 用 null 表示存在, 但不缓存.
        $this->setDefCache($defName, $def);
        return $def;
    }

    public function searchIds(string $wildcardId): array
    {
        return $this->getMetaRegistry()->searchIds($wildcardId);
    }

    public function searchIdExists(string $wildcardId): int
    {
        return $this->getMetaRegistry()->searchIdExists($wildcardId);
    }


    public function registerDef(Def $def, bool $notExists = true): bool
    {
        $this->checkExpire();
        $this->checkDefType(__METHOD__, $def);

        $name = $def->getName();
        if ($notExists && $this->hasDef($name)) {
            return false;
        }

        // 用 null 表示存在, 但不缓存.
        $this->setDefCache($name, $def);

        return $this->doRegisterDef($def, $notExists);
    }

    protected function setDefCache(string $name, Def $def) : void
    {
        $this->cachedDefs[$name] = $this->cacheExpire > 0
            ? $def
            : null;
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

    public function __destruct()
    {
        $this->cachedDefs = [];
        $this->mindset = null;
        $this->optRegistry = null;
    }

}