<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\Listeners\HearingHandler;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @mixin HearingHandler
 */
class FakeHearing
{

    /**
     * @var Navigator|null
     */
    protected $navigator;

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * FakeHearing constructor.
     * @param Dialog $dialog
     * @param Navigator|null $navigator
     */
    public function __construct(
        Dialog $dialog,
        Navigator $navigator
    )
    {
        $this->navigator = $navigator;
        $this->dialog = $dialog;
    }

    public function isIntent(
        string $intentName,
        callable $intentAction = null
    )
    {
        $session = $this->dialog->session;
        $repo = $session->intentRepo;
        if ($repo->hasDef($intentName)) {
            $id = $repo->getDef($intentName)->getName();
            $session->conversation
                ->getNLU()
                ->focusIntent($id);
        }

        return $this;
    }

    public function isIntentIn(
        array $intentNames,
        callable $intentAction = null
    ): Hearing
    {
        $session = $this->dialog->session;
        $repo = $session->intentRepo;
        foreach ($intentNames as $domain) {
            $names = $repo->getDefNamesByDomain($domain);
            foreach ($names as $name) {
                $this->isIntent($name);
            }
        }
        return $this;
    }


    public function end(callable $fallback = null) : Navigator
    {
        return $this->navigator ?? $this->dialog->missMatch();
    }

    public function __call($name, $arguments)
    {
        return $this;
    }
}