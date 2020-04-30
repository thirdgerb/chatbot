<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Snapshot;
use Commune\Protocals\Host\Convo\QuestionMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # properties
 * @property-read string $belongsTo
 * @property-read string $processId
 *
 * # frames
 * @property-read string $curFrameId
 * @property-read Task[] $tasks
 * @property-read string $rootTaskId
 *
 * ## before
 * @property-read string[] $yielding
 * @property-read int[] $blocking
 * @property-read string[][] $watching
 *
 * ## await
 * @property-read QuestionMsg|null $question
 * @property-read string[] $stageRoutes
 * @property-read string[] $contextRoutes
 * @property-read string $heed
 *
 * ## after
 * @property-read string[][] $sleeping
 * @property-read string[][] $gc
 *
 * # depending
 * @property-read string[][] $depending
 */
interface Process
{

    public function currentTask() : Task;

    public function rootTask() : Task;

    public function getTask(string $taskId) : Task;

    /*------ challenge ------*/

    public function challenge(Task $task, bool $force = false) : ? Task;


    /*------ block ------*/

    public function blockTask(Task $task) : void;

    public function popBlocking() : ? Task;

    /*------ watch ------*/

}