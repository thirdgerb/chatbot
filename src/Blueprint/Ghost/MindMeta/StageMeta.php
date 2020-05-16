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
use Commune\Support\Option\AbsMeta;
use Commune\Support\Option\Option;
use Commune\Support\Option\Wrapper;
use Commune\Support\Utils\StringUtils;


/**
 * Stage 的元数据.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read string $contextName
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $wrapper
 * @property-read array $config
 *
 */
class StageMeta extends AbsMeta
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'contextName' => '',
            'title' => 'contextTitle',
            'desc' => 'contextDesc',
            'wrapper' => '',
            'config' => [],
        ];
    }

    public function getId(): string
    {
        return $this->getFullStageName();
    }

    public static function createById($id, array $data = []): Option
    {
        $contextName = $data['contextName'] ?? '';
        $id = substr($id, 0, strlen($contextName) + 1);
        return parent::createById($id, $data);
    }

    public function getFullStageName() : string
    {
        return StringUtils::gluePrefixAndName(
            $this->contextName,
            $this->name,
            Context::NAMESPACE_SEPARATOR
        );
    }

    public static function relations(): array
    {
        return [];
    }

    /**
     * @return StageDef
     */
    public function getWrapper(): Wrapper
    {
        return parent::getWrapper();
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