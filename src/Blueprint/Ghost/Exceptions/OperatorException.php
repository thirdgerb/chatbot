<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Exceptions;

use Commune\Blueprint\Exceptions\HostRuntimeException;
use Commune\Blueprint\Ghost\Operator\Operator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OperatorException extends HostRuntimeException
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