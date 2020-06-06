<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;

use Commune\Blueprint\Ghost\Runtime\Task;
use Commune\Blueprint\Ghost\Runtime\Waiter;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Message\Host\QA\IQuestionMsg;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;
use Commune\Support\Message\Message;

/**
 * 进程序列化的压缩算法. 把常见的重复, 如 id, contextName, stageName, query 替换成整数序号.
 * 在调用栈很深的复杂多轮对话场景, 可能可以压缩 1/3 的数据量.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IProcessSerializer implements ProcessSerializer
{
    /**
     * @var int
     */
    public $idOrd = 0;

    /**
     * @var int
     */
    public $contextOrd = 0;

    /**
     * @var int
     */
    public $stageOrd = 0;

    /**
     * @var int
     */
    public $queryOrd = 0;

    /**
     * @var int[]
     */
    public $contexts = [];

    /**
     * @var int[]
     */
    public $stages = [];

    /**
     * @var int[]
     */
    public $ids = [];

    /**
     * @var int[]
     */
    public $queries = [];

    public function init() : void
    {
        $this->contextOrd = 0;
        $this->stageOrd = 0;
        $this->idOrd = 0;
        $this->queryOrd = 0;
        $this->ids = [];
        $this->stages = [];
        $this->contexts = [];
        $this->queries = [];
    }


    public function serialize(array $data) : string
    {
        $this->init();
        $result['_id'] = $this->_serializeId($data['_id']);
        $result['_belongsTo'] = $data['_belongsTo'];

        $tasks = $data['_tasks'] ?? [];
        foreach ($tasks as $id => $task) {
            $id = $this->_serializeId($id);
            $result['_tasks'][$id] = $this->_serializeTask($task);
        }

        $result['_root'] = $this->_serializeUcl(Ucl::decode($data['_root']));

        $waiter = $data['_waiter'];
        $result['_waiter'] = isset($waiter) ? $this->_serializeWaiter($waiter) : null;

        $result['_backtrace'] = array_map(
            function(Waiter $waiter){
                return $this->_serializeWaiter($waiter);
            },
            $data['_backtrace'] ?? []
        );

        $callbacks = $data['_callbacks'] ?? [];
        foreach ($callbacks as $id => $val) {
            $id = $this->_serializeId($id);
            $result['_callbacks'][$id] = $val;
        }

        $blocking = $data['_blocking'] ?? [];
        foreach ($blocking as $id => $val) {
            $id = $this->_serializeId($id);
            $result['_callbacks'][$id] = $val;
        }

        $sleeping = $data['_sleeping'] ?? [];
        foreach ($sleeping as $id => $stages) {
            $id = $this->_serializeId($id);
            $stages = array_map(
                function (string $stage) {
                    return $this->_serializeStage($stage);
                },
                $stages ?? []
            );
            $result['_sleeping'][$id] = $stages;
        }

        $depending = $data['_depending'] ?? [];
        foreach ($depending as $id => $str) {
            $id = $this->_serializeId($id);
            $str = $this->_serializeId($str);
            $result['_depending'][$id] = $str;
        }

        $dying = $data['_dying'] ?? [];
        foreach ($dying as $id => list ($turns, $stages)) {
            $id = $this->_serializeId($id);
            $stages = array_map(
                function(string $stage)  {
                    return $this->_serializeStage($stage);
                },
                $stages ?? []
            );
            $val = [$turns, $stages];
            $result['_dying'][$id] = $val;
        }

        $data = [
            $result,
            array_keys($this->ids),
            array_keys($this->contexts),
            array_keys($this->stages),
            array_keys($this->queries),
        ];

        $this->init();
        // return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // 可能比 json 会好一些. 毕竟json在 web 中传输, 各种反斜杠之类的规矩很多, 怕不够全面.
        return serialize($data);
    }

    public function unserialize(string $string) : array
    {
        $this->init();
        // $data = json_decode($string, true);
        $data = unserialize($string);

        $result = [];
        $info = $data[0];
        $this->ids = $data[1];
        $this->contexts = $data[2];
        $this->stages = $data[3];
        $this->queries = $data[4];

        $result['_id'] = $this->_unserializeId($info['_id']);
        $result['_belongsTo'] = $info['_belongsTo'];

        $tasks = $info['_tasks'] ?? [];
        foreach ($tasks as $id => $arr) {
            $id = $this->_unserializeId($id);
            $task = $this->_unserializeTask($arr);
            $result['_tasks'][$id] = $task;
        }

        $result['_root'] = $this->_unserializeUcl($info['_root'])->encode();
        $result['_waiter'] = $this->_unserializeWaiter($info['_waiter']);
        $result['_backtrace'] = array_map([$this, '_unserializeWaiter'], $info['_backtrace'] ?? []);

        $callbacks = $info['_callbacks'] ?? [];
        foreach ($callbacks as $id => $val) {
            $id = $this->_unserializeId($id);
            $result['_callbacks'][$id] = $val;
        }

        $blocking = $info['_blocking'] ?? [];
        foreach ($blocking as $id => $val) {
            $id = $this->_unserializeId($id);
            $result['_blocking'][$id] = $val;
        }

        $sleeping = $info['_sleeping'] ?? [];
        foreach ($sleeping as $id => $stages) {
            $id = $this->_unserializeId($id);
            $stages = array_map([$this, '_unserializeStage'], $stages);
            $result['_sleeping'][$id] = $stages;
        }

        $depending = $info['_depending'] ?? [];
        foreach ($depending as $id1 => $id2) {
            $id1 = $this->_unserializeId($id1);
            $id2 = $this->_unserializeId($id2);
            $result['_depending'][$id1] = $id2;
        }

        $dying = $info['_dying'] ?? [];
        foreach ($dying as $id => list($turns, $stages)) {
            $id = $this->_unserializeId($id);
            $stages = array_map([$this, '_unserializeStage'], $stages);
            $result['_dying'][$id] = [$turns, $stages];
        }

        $this->init();
        return $result;
    }

    public function _unserializeId(int $id) : string
    {
        return $this->ids[$id];
    }

    public function _serializeId(string $id) : int
    {
        if (isset($this->ids[$id])) {
            return $this->ids[$id];
        }
        $r = $this->ids[$id] = $this->idOrd;
        $this->idOrd ++;

        return $r;
    }

    public function _serializeContext(string $context) : int
    {
        if (isset($this->contexts[$context])) {
            return $this->contexts[$context];
        }
        $r = $this->contexts[$context] = $this->contextOrd;
        $this->contextOrd ++;
        return $r;
    }

    public function _unserializeContext(int $context) : string
    {
        return $this->contexts[$context];
    }

    public function _serializeStage(string $stage) : int
    {
        if (isset($this->stages[$stage])) {
            return $this->stages[$stage];
        }
        $r = $this->stages[$stage] = $this->stageOrd;
        $this->stageOrd ++;
        return $r;
    }

    public function _unserializeStage(int $stage) : string
    {
        return $this->stages[$stage];
    }
    
    public function _serializeQuery(string $query) : int
    {
        if (isset($this->queries[$query])) {
            return $this->queries[$query];
        }
        $r = $this->queries[$query] = $this->queryOrd;
        $this->queryOrd ++ ;
        return $r;
    }


    public function _unserializeQuery(int $query) : string
    {
        return $this->queries[$query];
    }


    public function _serializeTask(Task $task) : array
    {
        $ucl = $task->getUcl();
        $cancel = $task->watchCancel();
        $quit = $task->watchQuit();
        $result = [
            'u' => $this->_serializeUcl($ucl),
            'p' => array_map([$this, '_serializeStage'], $task->getPaths()),
            's' => $task->getStatus(),
            'c' => isset($cancel) ? $this->_serializeStage($cancel->stageName) : null,
            'q' => isset($quit) ? $this->_serializeStage($quit->stageName) : null,
        ];

        return $result;
    }

    public function _unserializeTask(array $arr) : Task
    {
        $uclArr = $arr['u'];
        $ucl = $this->_unserializeUcl($uclArr);
        $paths = $arr['p'];
        $paths = array_map([$this, '_unserializeStage'], $paths);
        $status = $arr['s'];
        $cancel = $arr['c'];
        $cancel = isset($cancel) ? $this->_unserializeStage($cancel) : null;

        $quit = $arr['q'];
        $quit = isset($quit) ? $this->_unserializeStage($quit) : null;

        return new ITask($ucl, $paths, $status, $cancel, $quit);
    }

    public function _serializeUcl(Ucl $ucl) : array
    {
        $result = [
            'c' => $this->_serializeContext($ucl->contextName),
            's' => $this->_serializeStage($ucl->stageName),
            'q' => $this->_serializeQuery($ucl->queryStr),
        ];

        return $result;
    }

    public function _unserializeUcl(array $arr) : Ucl
    {
        $context = $arr['c'];
        $stage = $arr['s'];
        $queryStr = $arr['q'];

        $contextName = $this->_unserializeContext($context);
        $stageName = $this->_unserializeStage($stage);
        $queryStr = $this->_unserializeQuery($queryStr);

        $query = Ucl::decodeQueryStr($queryStr);

        return Ucl::make($contextName, $query, $stageName);
    }


    public function _serializeWaiter(Waiter $waiter) : array
    {
        $question = $waiter->question;
        $result = [
            'u' => $this->_serializeUcl(Ucl::decode($waiter->await)),
            'q' => isset($question) ? $this->_serializeQuestion($waiter->question): null,
            'r' => array_map(
                function($route) {
                    return $this->_serializeUcl(Ucl::decode($route));
                },
                $waiter->routes
            ),
        ];

        return $result;
    }

    public function _unserializeWaiter(array $waiter) : Waiter
    {
        $serializedUcl = $waiter['u'];
        $question = $waiter['q'];
        $routes = $waiter['r'];

        return new IWaiter(
            $this->_unserializeUcl($serializedUcl)->encode(),
            isset($question) ? $this->_unserializeQuestion($question) : null,
            array_map([$this, '_unserializeUcl'], $routes)
        );
    }

    public function _serializeQuestion(QuestionMsg $question) : string
    {
        if ($question instanceof IQuestionMsg) {
            $name = get_class($question);
            $data = $question->toArray();
            $data['routes'] = array_map(function(string $route) {
                return $this->_serializeUcl(Ucl::decode($route));
            }, $data['routes'] ?? []);
            $question = [$name, $data];
        }

        return serialize($question);
    }

    public function _unserializeQuestion(string $question) : ? QuestionMsg
    {
        $q = unserialize($question);
        if ($q instanceof QuestionMsg) {
            return $q;
        }

        if (is_array($q)) {
            list($className, $data) = $q;

            $data['routes'] = array_map(function(array $route) {
                return $this->_unserializeUcl($route)->encode();
            }, $data['routes']);

            return call_user_func([$className, Message::CREATE_FUNC], $data);
        }

        return null;
    }



}