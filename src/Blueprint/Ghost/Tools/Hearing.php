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
 * 用户视角对话状态的回归.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Dialog $dialog
 */
interface Hearing extends Matcher
{
    public function getDialog() : Dialog;

    /**
     * @param callable|string $action
     * @return Hearing
     */
    public function action($action) : Hearing;


    /**
     * @param callable|string $action
     * @return Hearing
     */
    public function todo($action) : Hearing;


    /**
     * @param callable|string $action
     * @return Hearing
     */
    public function then($action = null) : Hearing;


    /**
     * @param callable|string $action
     * @return Hearing
     */
    public function component($action) : Hearing;


    /**
     * @param callable|string $action
     * @return Hearing
     */
    public function fallback($action) : Hearing;

    /**
     * @param callable|string|null $fallbackStrategy
     * @return Operator
     */
    public function end($fallbackStrategy = null) : Operator;
}