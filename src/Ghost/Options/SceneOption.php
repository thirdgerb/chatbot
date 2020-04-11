<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Options;

use Commune\Ghost\Blueprint\Session\Scene;
use Commune\Ghost\Prototype\Session\IScene;
use Commune\Support\Struct\AbsStruct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id
 * @property-read string $contextName
 * @property-read array $env
 */
class SceneOption extends AbsStruct
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => 'id',
            'contextName' => '',
            'env' => [
            ],
        ];
    }

    public function toSceneIns(array $env) : Scene
    {
        $data = [];
        foreach ($this->env as $key => $value) {
            $data[$key] = $env[$key] ?? $value;
        }

        return new IScene(
            $this->id,
            $this->contextName,
            $data
        );
    }


}