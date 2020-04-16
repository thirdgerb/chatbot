<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Routing;

use Commune\Ghost\Blueprint\Routing\Redirect;
use Commune\Ghost\Blueprint\Stage\Stage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRedirect implements Redirect
{
    /**
     * @var Stage
     */
    protected $stage;

    /**
     * IRedirect constructor.
     * @param Stage $stage
     */
    public function __construct(Stage $stage)
    {
        $this->stage = $stage;
    }


}