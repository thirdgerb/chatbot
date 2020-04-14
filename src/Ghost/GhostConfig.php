<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost;

use Commune\Ghost\Blueprint\Convo\Scene;
use Commune\Ghost\Options\KernelOption;
use Commune\Ghost\Options\SceneOption;
use Commune\Support\Struct\AbsStruct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *

 *
 * @property-read array $providers 需要注册的服务
 *  [
 *      'providerClass',  # 直接用类名来注册
 *      'providerClass' => [ configs ]  # 使用类名, 同时设定初始值
 *  ]
 *
 *
 * @property-read string $commandMark               命令的前缀. 最好带前缀.
 * @property-read string[] $comprehendPipes         尝试理解消息的管道.
 *
 * ## Scene
 *
 * @property-read SceneOption[] $scenes
 *
 * ## Kernel
 * @property-read string $kernel
 *
 * ## Session
 * @property-read int $sessionExpire
 *
 */
class GhostConfig extends AbsStruct
{
    protected static $associations = [
        'scenes[]' => SceneOption::class,
    ];


    /**
     * @var SceneOption[]
     */
    protected $sceneMap;

    public static function stub(): array
    {
        return [

        ];
    }

    /*------ scene ------*/

    public function hasScene(string $sceneId) : bool
    {
        $map = $this->getSceneMap();
        return array_key_exists($sceneId, $map);
    }

    /**
     * @return SceneOption[]
     */
    public function getSceneMap() : array
    {
        if (!isset($this->sceneMap)) {
            $map = [];
            foreach ($this->scenes as $scene) {
                $map[$scene->id] = $scene;
            }
            $this->sceneMap = $map;
        }

        return $this->sceneMap;
    }

    public function getScene(string $sceneId, array $data) : Scene
    {
        $map = $this->getSceneMap();
        $scene = $map[$sceneId] ?? reset($map);
        return $scene->toSceneIns($data);
    }

}