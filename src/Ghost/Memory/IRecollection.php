<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Memory;

use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\TArrayAccessToMutator;
use Commune\Support\Arr\TArrayData;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRecollection implements Recollection
{
    use ArrayAbleToJson, TArrayData;

    public function __construct()
    {
    }

}