<?php


namespace Commune\Demo\App\Cases\Maze\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;

class LocationInt extends MessageIntent
{
    const SIGNATURE = 'location';
    const DESCRIPTION = '查看坐标';

}