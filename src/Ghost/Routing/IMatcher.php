<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Routing;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Routing\Matcher;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMatcher implements Matcher
{

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * IMatcher constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
    }


}