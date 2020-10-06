<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cloner;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerAvatar;


/**
 * Avatar 默认的解决方案. 其实就是什么也不解决. 哈哈哈哈.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerAvatar implements ClonerAvatar
{
    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $id;

    /**
     * IClonerAvatar constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
    }


    public function getId(): string
    {
        if (isset($this->id)) {
            return $this->id;
        }

        // 这样对外暴露了 sessionId, 是有风险的.
        return $this->id
            ?? $this->id = $this->cloner->getSessionId();
    }

    public function getName(): string
    {
        return $this->name
            ?? $this->name = $this->cloner->ghost->getName();
    }

    public function getInfo(): array
    {
        return [];
    }


}