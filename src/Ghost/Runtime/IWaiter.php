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

use Commune\Blueprint\Ghost\Runtime\Waiter;
use Commune\Protocals\Host\Convo\QuestionMsg;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read null|QuestionMsg $question
 * @property-read string[] $stageRoutes
 * @property-read string[] $contextRoutes
 * @property-read string $ucl
 */
class IWaiter implements Waiter, ArrayAndJsonAble
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $ucl;

    /**
     * @var QuestionMsg|null
     */
    protected $question;

    /**
     * @var string[]
     */
    protected $stageRoutes;

    /**
     * @var string[]
     */
    protected $contextRoutes;

    /**
     * IWaiter constructor.
     * @param string $ucl
     * @param string[] $stageRoutes
     * @param string[] $contextRoutes
     * @param QuestionMsg|null $question
     */
    public function __construct(
        string $ucl,
        array $stageRoutes,
        array $contextRoutes,
        ?QuestionMsg $question
    )
    {
        $this->question = $question;
        $this->stageRoutes = $stageRoutes;
        $this->contextRoutes = $contextRoutes;
        $this->ucl = $ucl;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }


    public function __get($name)
    {
        return $this->{$name};
    }
}