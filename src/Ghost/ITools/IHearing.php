<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ITools;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Tools\Hearing;
use Commune\Ghost\IOperate\Tool\FakeHearing;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IHearing extends IMatcher implements Hearing
{
    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var callable|null
     */
    protected $todo;

    /**
     * @var Operator|null;
     */
    protected $nextOperator;

    /**
     * @var Hearing|null
     */
    protected $faker;

    /**
     * @var callable[]
     */
    protected $fallback = [];

    public function __construct(Dialog $dialog)
    {
        $this->dialog = $dialog;
        parent::__construct($dialog->cloner, []);
    }

    protected function call($caller)
    {
        return $this->dialog->caller()->call($caller, $this->matchedParams);
    }

    public function action($action): Hearing
    {
        if (!isset($this->nextOperator)) {
            $this->nextOperator = $this->call($action);
        }
        return $this;
    }

    protected function fakeHearing() : Hearing
    {
        return $this->faker
            ?? $this->faker = new FakeHearing($this);
    }

    public function todo($action): Hearing
    {
        if (isset($this->nextOperator)) {
            return $this->fakeHearing();
        }

        $this->then();

        $this->todo = $action;
        return $this;
    }

    public function then($action = null): Hearing
    {
        if (isset($this->nextOperator)) {
            return $this->fakeHearing();
        }

        if (!$this->matched) {
            return $this->refresh();
        }

        $todo = [];
        if (isset($this->todo)) {
            $todo[] = $this->todo;
            unset($this->todo);
        }

        if (isset($caller)) {
            $todo[] = $caller;
        }

        if (!empty($todo)) {
            foreach ($todo as $action) {
                $nav = $this->call($action);
                if ($nav instanceof Operator) {
                    $this->nextOperator = $nav;
                    break;
                }
            }
        }

        $this->refresh();

        return isset($this->nextOperator)
            ? $this->fakeHearing()
            : $this;
    }

    public function component($action): Hearing
    {
        return $action($this);
    }

    public function fallback($action): Hearing
    {
        $this->fallback[] = $action;
        return $this;
    }


    public function end() : Operator
    {
        foreach ($this->fallback as $fallback) {
            if (isset($this->nextOperator)) {
                break;
            }

            $next = $this->call($fallback);

            if ($next instanceof Dialog) {
                $this->nextOperator = $next;
                break;
            }
        }

        return $this->nextOperator ?? $this->dialog->confuse();
    }

    public function getDialog(): Dialog
    {
        return $this->dialog;
    }


    public function __destruct()
    {
        $this->fallback = [];
        $this->matchedParams = [];
        $this->matched = false;
        $this->dialog = null;
        $this->cloner = null;
        $this->input = null;
    }

}