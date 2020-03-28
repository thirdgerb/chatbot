<?php


namespace Commune\Support\RunningSpy;


/**
 * 由于有很多场景可能会产生内存泄露
 * 用这个类方便通过命令进行检测
 */
interface Spied
{
    /**
     * @param string $traceId
     * @param string $id
     */
    public function addRunningTrace(string $traceId, string $id) : void;

    /**
     * @param string $traceId
     */
    public function removeRunningTrace(string $traceId = null) : void;

    /**
     * @return string[]
     */
    public static function getRunningTraceKeys() : array;

    /**
     * @return string[] traceId => id
     */
    public static function getRunningTraces() : array;

}