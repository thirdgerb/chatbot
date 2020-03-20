<?php


namespace Commune\Components\Predefined\Intents\Attitudes;


use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;

class DontInt extends AttitudeInt implements Negative
{
    const SIGNATURE = 'dont';

    const DESCRIPTION = '拒绝';

}