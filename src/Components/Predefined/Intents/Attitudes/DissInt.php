<?php


namespace Commune\Components\Predefined\Intents\Attitudes;

use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;

class DissInt extends AttitudeInt implements Negative
{
    const SIGNATURE = 'diss';

    const DESCRIPTION = '批评';

}
