<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime\Operators;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operate\Finale;
use Commune\Blueprint\Ghost\Operate\Operator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Dumb extends AbsOperator implements Finale
{
    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * Dumb constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
    }


    protected function toNext(): Operator
    {
        $this->cloner->noState();
        return $this;
    }

    public function getOperatorDesc(): string
    {
        return static::class;
    }


}