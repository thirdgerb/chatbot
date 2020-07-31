<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Tools;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DialogContainer
{

    /**
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []);

    /**
     * @param callable|string $caller
     * @param array $parameters
     * @return mixed
     */
    public function call($caller, array $parameters = []);

    /**
     * @param callable $caller
     * @param array $parameters
     * @return bool
     */
    public function predict(callable $caller, array $parameters = []) : bool;

    /**
     * @param callable $caller
     * @param array $parameters
     * @return Dialog|null
     */
    public function action(callable $caller, array $parameters = []) : ? Operator;


}