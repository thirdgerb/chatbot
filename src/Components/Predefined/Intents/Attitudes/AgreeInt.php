<?php


namespace Commune\Components\Predefined\Intents\Attitudes;


use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;

class AgreeInt extends AttitudeInt implements Positive
{
    const SIGNATURE = 'agree';

    const DESCRIPTION = '同意';

}