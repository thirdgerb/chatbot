<?php

namespace Commune\Components\Predefined\Intents\Attitudes;

use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;

/**
 * 表示感谢
 */
class ThanksInt extends AttitudeInt implements Positive
{
    const SIGNATURE = 'thanks';

    const DESCRIPTION = '表示感谢';

}