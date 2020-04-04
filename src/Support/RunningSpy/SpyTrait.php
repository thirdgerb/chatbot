<?php


namespace Commune\Support\RunningSpy;


trait SpyTrait
{
    protected static $runningSpyTraces = [];

    /**
     * 通常放在 __construct() 方法
     *
     * @param string $traceId
     * @param string $id
     */
    public function addRunningTrace(string $traceId, string $id): void
    {
        // 如果功能没启用, 则不存储
        if (!SpyAgency::isRunning()) {
            return;
        }

        self::$runningSpyTraces[$traceId] = $id;
        SpyAgency::addSpy(static::class);
    }

    /**
     * 通常放在 __destruct 方法
     * @param string|null $traceId
     */
    public function removeRunningTrace(string $traceId = null): void
    {
        // 如果功能没启用, 则不存储
        if (!SpyAgency::isRunning()) {
            return;
        }

        if (isset($traceId)) {
            unset(self::$runningSpyTraces[$traceId]);
        }
    }

    /**
     * 用来排查
     * @return array
     */
    public static function getRunningTraceKeys(): array
    {
        return array_keys(self::$runningSpyTraces);
    }

    /**
     * 用来排查
     * @return array
     */
    public static function getRunningTraces(): array
    {
        return self::$runningSpyTraces;
    }


}