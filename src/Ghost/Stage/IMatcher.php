<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Callables\Prediction;
use Commune\Blueprint\Ghost\Stage\Matcher;
use Commune\Blueprint\Ghost\Stage\Stage;
use Commune\Protocals\HostMsg;
use Commune\Support\SoundLike\SoundLikeInterface;
use Illuminate\Support\Collection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMatcher implements Matcher
{

    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var HostMsg
     */
    protected $message;

    /**
     * IMatcher constructor.
     * @param Stage $stage
     * @param HostMsg $message
     */
    public function __construct(Stage $stage, HostMsg $message)
    {
        $this->stage = $stage;
        $this->message = $message;
    }



}