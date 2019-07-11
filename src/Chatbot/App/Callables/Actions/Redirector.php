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