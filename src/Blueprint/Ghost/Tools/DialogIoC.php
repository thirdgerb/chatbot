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
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DialogIoC
{


    /**
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     *
     * @throws
     * 实际上是 throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function make(string $abstract, array $parameters = []);

    /**
     * @param callable $caller
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    public function call(callable $caller, array $parameters = []);

    public function predict(callable $caller) : bool;

    public function action(callable $caller) : ? Dialog;


}