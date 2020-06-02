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
use Commune\Blueprint\Ghost\MindDef\AliasesForStage;

/**
 * Stage 的元数据.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read string $stageName
 * @property-read string $contextName
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $wrapper
 * @property-read array $config
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

    public function __get_wrapper() : string
    {
        return AliasesForStage::getOriginFromAlias($this->_data['wrapper'] ?? '');
    }

    public function __set_wrapper(string $name, $wrapper) : void
    {
        $this->_data[$name] = AliasesForStage::getAliasOfOrigin(strval($wrapper));
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