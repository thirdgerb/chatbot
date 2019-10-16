<?php


namespace Commune\Components\Predefined\Intents\Attitudes;

use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;

/**
 * 表示否认的意图.
 */
class DenyInt extends AttitudeInt implements Negative
{
    const SIGNATURE = 'deny';

    const DESCRIPTION = '否认';
}