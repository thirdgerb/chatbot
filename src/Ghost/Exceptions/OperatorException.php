<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Exceptions;

use Commune\Framework\Exceptions\AppRuntimeException;
use Commune\Ghost\Blueprint\Operator\Operator;


/**
 * 运行多轮对话的逻辑中, 允许通过异常来中断复杂的逻辑
 * 直接指定下一步方向.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OperatorException extends AppRuntimeException
{
    /**
     * @var Operator
     */
    protected $operator;

    /**
     * OperatorException constructor.
     * @param Operator $operator
     */
    public function __construct(Operator $operator)
    {
        $this->operator = $operator;
        parent::__construct();
    }

    /**
     * @return Operator
     */
    public function getOperator(): Operator
    {
        return $this->operator;
    }




}