<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Convo;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Exceptions\Logic\InvalidConfigException;
use Commune\Blueprint\Ghost\Convo\ConvoScene;
use Commune\Protocals\Intercom\GhostInput;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sceneId
 * @property-read string $contextName
 * @property-read array $env
 */
class IConvoScene implements ConvoScene
{

    protected $sceneId;

    protected $contextName;

    protected $env;

    public function __construct(GhostInput $input, GhostConfig $config)
    {
        $sceneId = $input->sceneId;

        if (!isset($config->sceneContextNames[$sceneId])) {
            $sceneId = $config->defaultScene;
        }
        $this->sceneId = $sceneId;
        $contextName = $config->sceneContextNames[$sceneId] ?? null;
        if (empty($contextName)) {
            throw new InvalidConfigException(
                GhostConfig::class,
                'sceneId'
            );
        }
        $this->env = $input->env;
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}