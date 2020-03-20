<?php


namespace Commune\Chatbot\App\Contexts;

use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 标准的多轮对话 Task 单元
 * 一个 task 完成一个任务, task 可以用各种方式嵌套.
 *
 *
 * 可以用 __on{$name}(Checkpoint $stage) : Navigator 的方式定义多个stage
 */
abstract class TaskDef extends OOContext
{
    /**
     * @var string
     */
    const DESCRIPTION = 'should define description';

    abstract public function __onStart(Stage $stage): Navigator;


}