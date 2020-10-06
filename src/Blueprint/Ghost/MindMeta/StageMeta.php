<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindMeta;

use Commune\Support\Option\AbsMeta;
use Commune\Ghost\Support\ContextUtils;
use Commune\Blueprint\Ghost\MindDef\StageDef;

/**
 * Stage 的元数据.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $name
 * @property string $stageName
 * @property string $contextName
 * @property string $title
 * @property string $desc
 * @property string $wrapper
 * @property array $config
 *
 * @method StageDef toWrapper(): Wrapper
 */
class StageMeta extends AbsMeta implements DefMeta
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'stageName' => '',
            'contextName' => '',
            'name' => '',
            'title' => '',
            'desc' => '',
            'wrapper' => '',
            'config' => [],
        ];
    }

    /**
     * @param array $data
     * @param string $contextName
     * @param string $shortName
     * @param bool $force
     * @return array
     */
    public static function mergeContextInfo(
        array $data,
        string $contextName,
        string $shortName,
        bool $force = false
    ) : array
    {

        if (empty($data['contextName']) || $force) {
            $data['contextName'] = $contextName;
        }

        $stageName = $data['stageName'] ?? '';
        $stageName = empty($stageName) || $force
            ? $shortName
            : $stageName;
        $data['stageName'] = $stageName;

        $data['name'] = ContextUtils::makeFullStageName(
            $contextName,
            $shortName
        );

        return $data;
    }

    public static function relations(): array
    {
        return [];
    }
}