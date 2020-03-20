<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Dialogue\Hearing\HearingHandler;
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

    protected $isStart;

    /**
     * FakeHearing constructor.
     * @param Dialog $dialog
     * @param Navigator $navigator
     * @param bool $isStart
     */
    public function __construct(
        Dialog $dialog,
        Navigator $navigator,
        bool $isStart
    )
    {
        $this->navigator = $navigator;
        $this->dialog = $dialog;
        $this->isStart = $isStart;
    }

    public function isIntent(
        string $intentName,
        callable $intentAction = null
    )
    {
        if (!$this->isStart) {
            return $this;
        }

        // start 状态下, 会记录试图匹配的 intent 到 NLU
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