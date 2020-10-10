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
use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocols\HostMsg\Convo\QA\QuestionMsg;
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
    protected $_routes;

    /**
     * IWaiter constructor.
     * @param string $ucl
     * @param QuestionMsg|null $question
     * @param Ucl[] $routes
     */
    public function __construct(
        string $ucl,
        ?QuestionMsg $question,
        array $routes
    )
    {
        $this->_await = strval($ucl);
        $this->_question = $question;
        $this->_routes = array_values(
            array_map(
                function ($route) {
                    return strval($route);
                },
                $routes
            )
        );
    }


    public function toArray(): array
    {
        return [
            'await' => $this->_await,
            'question' => isset($this->_question) ? $this->_question->toArray() : null,
            'routes' => $this->_routes,
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