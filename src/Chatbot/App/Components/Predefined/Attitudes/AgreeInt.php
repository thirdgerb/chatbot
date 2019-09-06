<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;

class AgreeInt extends AttitudeInt implements Positive
{
    const SIGNATURE = 'agree';

    const DESCRIPTION = '同意';

}