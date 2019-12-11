<?php


namespace Commune\Chatbot\Framework\Conversation;


trait RunningSpyTrait
{
    protected static $runningSpyTraces = [];

    public function addRunningTrace(string $traceId, string $id): void
    {
        // 如果功能没启用, 则不存储
        if (!RunningSpies::isRunning()) {
            return;
        }

        self::$runningSpyTraces[$traceId] = $id;
        RunningSpies::addSpy(static::class);
    }

    public function removeRunningTrace(string $traceId = null): void
    {
        // 如果功能没启用, 则不存储
        if (!RunningSpies::isRunning()) {
            return;
        }

        if (isset($traceId)) {
            unset(self::$runningSpyTraces[$traceId]);
        }
    }

    public static function getRunningTraceKeys(): array
    {
        return array_keys(self::$runningSpyTraces);
    }

    public static function getRunningTraces(): array
    {
        return self::$runningSpyTraces;
    }


}