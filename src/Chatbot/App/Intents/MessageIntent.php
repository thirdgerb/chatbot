<?php


namespace Commune\Chatbot\App\Intents;


use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Context\Intent\IntentMatcherOption;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

abstract class MessageIntent extends AbsCmdIntent
{
    const DESCRIPTION = 'should define description';

    const SIGNATURE = ''; // must be set
    const REGEX = [];
    const KEYWORDS = [];

    public static function getMatcherOption(): IntentMatcherOption
    {
        if (empty(static::SIGNATURE)) {
            throw new ConfigureException(
                __METHOD__
                . ' need signature to define entities,'
                . ' empty value given'
            );
        }

        return new IntentMatcherOption([
            'signature' => static::SIGNATURE,
            'regex' => static::REGEX,
            'keywords' => static::KEYWORDS,
        ]);
    }


    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->dependOn($this);
    }

    public function __onStart(Stage $stageRoute): Navigator
    {
        return $stageRoute->dialog->fulfill();
    }

}