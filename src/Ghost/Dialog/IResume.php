<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\Ucl;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class IResume extends AbsDialog implements Resume
{

    public function __construct(Dialog $dialog, Ucl $ucl)
    {
        parent::__construct(
            $dialog->cloner,
            $ucl,
            $dialog
        );
    }

}