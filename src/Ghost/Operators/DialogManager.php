<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Exceptions\OperatorException;
use Commune\Blueprint\Ghost\Exceptions\TooManyOperatorsException;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Ghost\Operators\Start\ProcessStart;
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
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var Runtime
     */
    protected $runtime;

    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
        $this->uuid = $cloner->getUuid();
        $this->runtime = $cloner->runtime;
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
                $operator = $operator->invoke($this->cloner);
            }


        // 拿到了一个用异常做的重定向算子
        } catch (OperatorException $e) {

            // 重定向到新的逻辑流程.
            return $this->runDialogManage($e->getOperator());

        // 超过算子的最大数量.
        } catch (TooManyOperatorsException $e) {

            // todo

        // 无法处理的异常.
        } catch (\Throwable $e) {

        }

        return true;
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->uuid);
        $this->cloner = null;
        $this->runtime = null;
    }

}