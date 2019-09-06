<?php


namespace Commune\Chatbot\App\Callables\Actions;


use Commune\Chatbot\OOHost\Dialogue\Dialog;

/**
 * 方便定义极简的 callable 对象.
 *
 * 比如 [Redirect::class, 'next'] , 不需要写闭包.
 */
class Redirector
{

    public static function goStage(string $stage) : \Closure
    {
        return function(Dialog $dialog) use ($stage){
            return $dialog->goStage($stage);
        };
    }

    public static function goStageThrough(array $stages) : \Closure
    {
        return function(Dialog $dialog) use ($stages) {
            return $dialog->goStagePipes($stages);
        };
    }

    public static function goBackward() : callable
    {
        return [static::class, str_replace('go', '', __FUNCTION__)];
    }

    public static function goNext() : callable
    {
        return [static::class, str_replace('go', '', __FUNCTION__)];
    }

    public static function goFulfill() : callable
    {
        return [static::class, str_replace('go', '', __FUNCTION__)];
    }

    public static function goRepeat() : callable
    {
        return [static::class, str_replace('go', '', __FUNCTION__)];
    }

    public static function goRewind() : callable
    {
        return [static::class, str_replace('go', '', __FUNCTION__)];
    }

    public static function goCancel() : callable
    {
        return [static::class, str_replace('go', '', __FUNCTION__)];
    }

    public static function goRestart() : callable
    {
        return [static::class, str_replace('go', '', __FUNCTION__)];
    }

    public static function goWait() : callable
    {
        return [static::class, str_replace('go', '', __FUNCTION__)];
    }



    public static function backward(Dialog $dialog)
    {
        return $dialog->backward();
    }

    public static function next(Dialog $dialog)
    {
        return $dialog->next();
    }

    public static function fulfill(Dialog $dialog)
    {
        return $dialog->fulfill();
    }

    public static function repeat(Dialog $dialog)
    {
        return $dialog->repeat();
    }

    public static function rewind(Dialog $dialog)
    {
        return $dialog->rewind();
    }

    public static function cancel(Dialog $dialog)
    {
        return $dialog->cancel();
    }

    public static function restart(Dialog $dialog)
    {
        return $dialog->restart();
    }

    public static function wait(Dialog $dialog)
    {
        return $dialog->wait();
    }

}