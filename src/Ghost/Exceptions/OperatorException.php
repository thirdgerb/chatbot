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

use Commune\Ghost\Blueprint\Operator\Operator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OperatorException extends \RuntimeException
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


}