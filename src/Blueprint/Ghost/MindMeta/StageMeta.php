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

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Alias\TAliases;
use Commune\Support\Option\AbsMeta;

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
 */
class StageMeta extends AbsMeta
{
    use TAliases;

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
        return self::getOriginFromAlias($this->_data['wrapper'] ?? '');
    }

    public function __set_wrapper(string $name, $wrapper) : void
    {
        $this->_data[$name] = self::getAliasOfOrigin(strval($wrapper));
    }

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

    public static function validate(array $data): ? string /* errorMsg */
    {
        $name = $data['name'] ?? '';
        if (ContextUtils::isValidStageName($name)) {
            return "stage name $name is invalid";
        }

        return parent::validate($data);
    }

    public static function validateWrapper(string $wrapper): ? string
    {
        $defType = StageDef::class;
        return is_a($wrapper, $defType, TRUE)
            ? null
            : static::class . " wrapper should be subclass of $defType, $wrapper given";
    }

}