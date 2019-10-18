<?php


namespace Commune\Components\Predefined\Intents\Attitudes;


use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;

class AffirmInt extends AttitudeInt implements Positive
{
    const SIGNATURE = 'affirm';

    const DESCRIPTION = '确认';

    const REGEX = [
        ['/^(是|好|对|y|1)$/']
    ];
}