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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read null|QuestionMsg $question
 * @property-read string[] $stageRoutes
 * @property-read string[] $contextRoutes
 * @property-read string $await
 */
class IWaiter implements Waiter
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $_await;

    /**
     * @var QuestionMsg|null
     */
    protected $_question;

    /**
     * @var string[]
     */
    protected $_stageRoutes;

    /**
     * @var string[]
     */
    protected $_contextRoutes;

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
        $this->_question = $question;
        $this->_stageRoutes = $stageRoutes;
        $this->_contextRoutes = $contextRoutes;
        $this->_await = $ucl;
    }

    public function toArray(): array
    {
        return [
            'await' => $this->_await,
            'stageRoutes' => $this->_stageRoutes,
            'contextRoutes' => $this->_contextRoutes,
            'question' => isset($this->_question) ? $this->_question->toArray() : null
        ];
    }


    public function __get($name)
    {
        $name = "_$name";
        return property_exists($this, $name)
            ? $this->{$name}
            : null;
    }
}