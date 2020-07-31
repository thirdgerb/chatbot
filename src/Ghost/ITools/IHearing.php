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
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Protocals\HostMsg\Convo\EventMsg;

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

    /**
     * @return static
     */
    public function refresh(): Matcher
    {
        unset($this->todo);
        $this->fallback = [];
        return parent::refresh();
    }

    protected function call($caller)
    {
        return $this->dialog->container()->call($caller, $this->matchedParams);
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

        if (isset($action)) {
            $todo[] = $action;
        }

        if (!empty($todo)) {
            foreach ($todo as $caller) {
                $nav = $this->call($caller);
                if ($nav instanceof Operator) {
                    $this->nextOperator = $nav;
                    break;
                }
            }
        }

        unset($todo);
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


    public function end($action = null) : Operator
    {
        $this->then();

        // Event 消息如果不主动处理, 则完全忽略.
        if ($this->input->getMessage() instanceof EventMsg) {
            return $this->dialog->dumb();
        }

        if (isset($action)) {
            $this->fallback[] = $action;
        }

        foreach ($this->fallback as $fallback) {
            if (isset($this->nextOperator)) {
                break;
            }

            $next = $this->call($fallback);

            if ($next instanceof Operator) {
                $this->nextOperator = $next;
                break;
            }
        }

        $next = $this->nextOperator ?? $this->dialog->confuse();
        $this->destroy();
        return $next;
    }

    protected function destroy()
    {
        unset(
            $this->faker,
            $this->dialog,
            $this->cloner,
            $this->todo,
            $this->fallback
        );
    }

    public function getDialog(): Dialog
    {
        return $this->dialog;
    }


    public function __destruct()
    {
        unset(
            $this->todo,
            $this->fallback,
            $this->nextOperator
        );

        parent::__destruct();
    }

}