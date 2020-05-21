<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Tools;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Tools\Hearing;
use Commune\Blueprint\Ghost\Tools\Navigator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IHearing extends IMatcher implements Hearing
{
    protected $dialog;

    /**
     * @var callable|null
     */
    protected $todo;

    /**
     * @var Dialog|null;
     */
    protected $nextDialog;

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

    protected function fakeHearing() : Hearing
    {
        return $this->faker
            ?? $this->faker = new FakeHearing($this);
    }

    public function nav(): Navigator
    {
        return $this->dialog->nav();
    }

    public function todo(callable $caller): Hearing
    {
        if (isset($this->nextDialog)) {
            return $this->fakeHearing();
        }

        $this->then();

        $this->todo = $caller;
        return $this;
    }

    public function then(callable $caller = null): Hearing
    {
        if (isset($this->nextDialog)) {
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
                if ($nav instanceof Dialog) {
                    $this->nextDialog = $nav;
                    break;
                }
            }
        }

        return $this->refresh();
    }

    public function component(callable $caller): Hearing
    {
        return $caller($this);
    }

    public function fallback(callable $caller): Hearing
    {
        $this->fallback[] = $caller;
        return $this;
    }


    public function end(): Dialog
    {
        foreach ($this->fallback as $fallback) {
            if (isset($this->nextDialog)) {
                break;
            }

            $next = $this->call($fallback);

            if ($next instanceof Dialog) {
                $this->nextDialog = $next;
                break;
            }
        }

        return $this->nextDialog ?? $this->dialog->nav()->confuse();
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