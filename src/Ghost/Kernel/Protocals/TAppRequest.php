<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Kernel\Protocals;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @mixin AppRequest
 */
trait TAppRequest
{
    public function isValid(): bool
    {
        $error = $this->isInvalid();
        return is_null($error);
    }

    public function isInvalid(): ? string
    {
        return $this->getInput()->isInvalid();
    }

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }


}