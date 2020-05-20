<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerInstance;
use Commune\Blueprint\Ghost\Cloner\ClonerInstanceStub;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ContextStub implements ClonerInstanceStub
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $ucl;

    /**
     * ContextStub constructor.
     * @param string $ucl
     */
    public function __construct(string $ucl)
    {
        $this->ucl = $ucl;
    }

    public function toInstance(Cloner $cloner): ClonerInstance
    {
        $uclObj = Ucl::decodeUclStr($this->ucl);
        return $cloner->getContext($uclObj);
    }


    public function toArray(): array
    {
        return [
            'type' => static::class,
            'ucl' => $this->ucl
        ];
    }


}