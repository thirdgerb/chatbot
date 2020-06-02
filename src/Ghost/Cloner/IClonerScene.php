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
use Commune\Framework\Spy\SpyAgency;
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

    protected $_sceneId;

    protected $_root;

    protected $_env;

    public function __construct(InputMsg $input, GhostConfig $config)
    {
        $sceneId = $input->getSceneId();

        if (!isset($config->sceneContextNames[$sceneId])) {
            $sceneId = '';
        }

        // 重置场景 ID
        $this->_sceneId = $sceneId;
        $input->setSceneId($sceneId);
        $contextName = $config->sceneContextNames[$sceneId] ?? $config->defaultContextName;

        if (empty($contextName)) {
            throw new InvalidConfigException(
                GhostConfig::class,
                'sceneId'
            );
        }
        $this->_root = Ucl::decodeUclStr($contextName);
        $this->_env = $input->getEnv();

        SpyAgency::incr(static::class);
    }

    public function __get($name)
    {
        return $this->{"_$name"};
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}