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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DialogIoC
{
    /**
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []);

    /**
     * @param callable|string $caller
     * @param array $parameters
     * @return mixed
     */
    public function call($caller, array $parameters = []);

    public function predict(callable $caller) : bool;

    public function action(callable $caller) : ? Dialog;


}