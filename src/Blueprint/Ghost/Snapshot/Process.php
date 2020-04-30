<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Snapshot;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # properties
 * @property-read string $belongsTo
 * @property-read string $processId
 *
 * # frames
 * @property-read string $curFrameId
 * @property-read Frame[] $frames
 * @property-read string $rootFrameId
 *
 * # eventMap
 * @property-read ReactsMap $reacts
 *
 * # depending
 * @property-read string[][] $depending
 */
interface Process
{

    public function currentFrame() : Frame;

    public function rootFrame() : Frame;

    /*------ broker ------*/


}