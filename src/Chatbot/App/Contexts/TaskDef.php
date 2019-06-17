<?php


namespace Commune\Chatbot\App\Contexts;

use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 标准的Task.
 * 可以用 __on{$name}(Checkpoint $stage) : Navigator 的方式定义多个stage
 */
abstract class TaskDef extends OOContext
{
    /**
     * @var string
     */
    const DESCRIPTION = 'should define description';

    /**
     * 定义 task 的基础属性.
     * 当这些属性都存在时, 才会正式进入 start
     *
     * @param Depending $depending
     */
    abstract public static function __depend(Depending $depending): void;

    abstract public function __onStart(Stage $stage): Navigator;

    abstract public function __exiting(Exiting $listener): void;



}