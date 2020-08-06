<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\RuntimeDriver;

use Commune\Contracts\Cache;
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Framework\FileCache\FileCacheOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Blueprint\Ghost\Cloner\ClonerLogger;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DemoRuntimeDriver extends CachableRuntimeDriver
{

    /**
     * @var OptRegistry
     */
    protected $registry;

    /**
     * TestCacheRuntimeDriver constructor.
     * @param Cache $cache
     * @param ClonerLogger $logger
     * @param OptRegistry $registry
     */
    public function __construct(
        Cache $cache,
        ClonerLogger $logger,
        OptRegistry $registry
    )
    {
        $this->registry = $registry;
        parent::__construct($cache, $logger);
    }

    public function saveLongTermMemories(
        string $clonerId,
        array $memories
    ): bool
    {
        $category = $this->registry->getCategory(FileCacheOption::class);
        foreach ($memories as $memory) {
            /**
             * @var Memory $memory
             */
            $id = $this->getLongTermMemoryId($clonerId, $memory->getId());
            $option = new FileCacheOption([
                'id' => $id,
                'serialized' => serialize($memory)
            ]);

            $category->save($option);
        }

        return true;
    }

    public function findLongTermMemories(string $clonerId, string $memoryId): ? Memory
    {
        $id = $this->getLongTermMemoryId($clonerId, $memoryId);
        $category = $this->registry->getCategory(FileCacheOption::class);
        if (!$category->has($id)) {
            return null;
        }

        /**
         * @var FileCacheOption $option
         */
        $option = $category->find($id);
        $serialized = $option->serialized;

        $memory = unserialize($serialized);
        if ($memory instanceof Memory) {
            return $memory;
        }

        return null;
    }

    protected function getLongTermMemoryId(string $clonerId, string $memoryId) : string
    {
        return "clone:$clonerId:memory:$memoryId";
    }

}