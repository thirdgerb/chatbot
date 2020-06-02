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

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Exceptions\Logic\InvalidConfigException;
use Commune\Blueprint\Ghost\Cloner\ClonerScene;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sceneId
 * @property-read Ucl $root
 * @property-read array $env
 */
class IClonerScene implements ClonerScene
{

    protected $sceneId;

    protected $root;

    protected $env;

    public function __construct(InputMsg $input, GhostConfig $config)
    {
        $sceneId = $input->getSceneId();

        if (!isset($config->sceneContextNames[$sceneId])) {
            $sceneId = '';
        }

        // 重置场景 ID
        $this->sceneId = $sceneId;
        $input->setSceneId($sceneId);
        $contextName = $config->sceneContextNames[$sceneId] ?? $config->defaultContextName;

        if (empty($contextName)) {
            throw new InvalidConfigException(
                GhostConfig::class,
                'sceneId'
            );
        }
        $this->root = Ucl::decodeUclStr($contextName);
        $this->env = $input->getEnv();
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}