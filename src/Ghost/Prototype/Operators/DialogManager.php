<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Runtime;
use Commune\Ghost\Exceptions\OperatorException;
use Commune\Ghost\Exceptions\TooManyOperatorsException;
use Commune\Ghost\Prototype\Operators\Start\ProcessStart;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


/**
 * 多轮对话管理器, 负责运行多轮对话的所有逻辑, 通过 Operator 算子的方式.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DialogManager implements Spied
{
    use SpyTrait;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var Conversation
     */
    protected $convo;

    /**
     * @var Runtime
     */
    protected $runtime;

    /**
     * DialogManager constructor.
     * @param Conversation $convo
     */
    public function __construct(Conversation $convo)
    {
        $this->convo = $convo;
        $this->uuid = $convo->getUuid();
        $this->runtime = $convo->runtime;
        static::addRunningTrace($this->uuid, $this->uuid);
    }


    public function runDialogManage(Operator $operator = null) : bool
    {
        $operator = $operator ?? new ProcessStart();
        $trace = $this->runtime->trace;

        try {

            // 循环计算
            while(isset($operator)) {

                // 记录算子的路径.
                // 超出最大重定向记录的话, 会抛出异常.
                $trace->record($operator);

                // 运行算子.
                $operator = $operator->invoke($this->convo);
            }


        // 拿到了一个用异常做的重定向算子
        } catch (OperatorException $e) {

            // 重定向到新的逻辑流程.
            return $this->runDialogManage($e->getOperator());

        // 超过算子的最大数量.
        } catch (TooManyOperatorsException $e) {

        // 无法处理的异常.
        } catch (\Throwable $e) {

        }

        return true;
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->uuid);
        $this->convo = null;
        $this->runtime = null;
    }

}