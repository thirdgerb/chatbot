<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Libs;

use Commune\Contracts\Cache;
use Commune\Components\HeedFallback\Data\FallbackSceneOption;
use Commune\Contracts\Log\ExceptionReporter;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FallbackSceneRepoDemo implements FallbackSceneRepository
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var ExceptionReporter
     */
    protected $reporter;

    protected $appId;

    /**
     * FallbackSceneRepoDemo constructor.
     * @param Cache $cache
     * @param ExceptionReporter $reporter
     * @param $appId
     */
    public function __construct(Cache $cache, ExceptionReporter $reporter, $appId = 'demo')
    {
        $this->cache = $cache;
        $this->reporter = $reporter;
        $this->appId = $appId;
    }


    protected function pipelineKey() : string
    {
        $appId = $this->appId;
        return "commune:$appId:fallback:scene:pipe";
    }

    protected function hashMapKey() : string
    {
        $appId = $this->appId;
        return "commune:$appId:fallback:scene:map";
    }

    public function count(): int
    {
        $arr = $this->getPipeArr();
        return count($arr);
    }

    protected function getPipeArr() : array
    {
        $key = $this->pipelineKey();
        $data = $this->cache->get($key);
        if (empty($data)) {
            return [];
        }

        $arr = json_decode($data, true);
        if (!is_array($arr)) {
            $this->cache->forget($key);
            return [];
        }

        return $arr;
    }

    protected function savePipeArr(array $ids) : bool
    {
        $key = $this->pipelineKey();
        return $this->cache->set($key, json_encode($ids));
    }

    public function push(FallbackSceneOption $option, bool $toPipe = true): bool
    {
        $key = $this->hashMapKey();
        $data = $option->toJson();
        $success = $this->cache->hSet($key, $option->batchId, $data);
        if ($success && $toPipe) {
            $arr = $this->getPipeArr();
            array_push($arr, $option->batchId);
            return $this->savePipeArr($arr);
        }
        return $success;
    }

    public function find(string $id): ? FallbackSceneOption
    {
        $key = $this->hashMapKey();
        $data = $this->cache->hGet($key, $id);
        if (empty($data)) {
            return null;
        }

        $un = json_decode($data, true);
        if (empty($un)) {
            $this->cache->hDel($key, $id);
        }

        try {
            return new FallbackSceneOption($un);
        } catch (\Throwable $e) {
            $this->reporter->report($e);
            $this->cache->hDel($key, $id);
            return null;
        }
    }

    public function pop(): ? FallbackSceneOption
    {
        $arr = $this->getPipeArr();
        $id = array_shift($arr);
        if (empty($id)) {
            return null;
        }
        $this->savePipeArr($arr);
        return $this->find($id);
    }

    public function delete(string $id): bool
    {
        $key = $this->hashMapKey();
        return $this->cache->hDel($key, $id);
    }

    public function flush() : void
    {
        $pipeKey = $this->pipelineKey();
        $dataKey = $this->hashMapKey();
        $this->cache->forget($pipeKey);
        $this->cache->forget($dataKey);
    }

}