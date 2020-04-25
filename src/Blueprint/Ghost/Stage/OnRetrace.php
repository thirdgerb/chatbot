<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Stage;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Blueprint\Ghost\Routing\Fallback;
use Commune\Blueprint\Ghost\Routing\Staging;

/**
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Cloner $cloner
 * @property-read StageDef $def
 * @property-read Context $self
 * @property-read Context $from
 */
interface OnRetrace extends Stage
{
    public function staging() : Staging;

    public function fallback() : Fallback;

}