<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Traits;

use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Ghost\Context\IContext;
use Commune\Support\Option\AbsOption;
use Commune\Blueprint\Ghost\MindDef\AliasesForAuth;
use Commune\Blueprint\Ghost\MindDef\AliasesForContext;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $contextWrapper
 *
 *
 * @mixin AbsOption
 * @mixin ContextDef
 */
trait ContextDefTrait
{

    public static function validate(array $data): ? string /* errorMsg */
    {
        $name = $data['name'] ?? '';

        if (!ContextUtils::isValidContextName($name)) {
            return "contextName $name is invalid";
        }

        return parent::validate($data);
    }

    public function __get_contextWrapper() : string
    {
        $wrapper = $this->_data['contextWrapper'] ?? '';
        $wrapper = empty($wrapper)
            ? IContext::class
            : $wrapper;

        return AliasesForContext::getOriginFromAlias($wrapper);
    }

    public function __set_contextWrapper($name, $value) : void
    {
        $this->_data[$name] = AliasesForContext::getAliasOfOrigin($value);
    }

    public function __set_auth($name, $value) : void
    {
        $this->_data[$name] = array_map(
            [AliasesForAuth::class, AliasesForAuth::FUNC_GET_ALIAS],
            $value
        );

    }

    public function __get_auth() : array
    {
        $auth = $this->_data['auth'] ?? [];
        return array_map(
            [AliasesForAuth::class, AliasesForAuth::FUNC_GET_ORIGIN],
            $auth
        );
    }


    /*------ wrap -------*/

    /**
     * @return ContextMeta
     */
    public function toMeta(): Meta
    {
        $config = $this->toArray();
        unset($config['name']);
        unset($config['title']);
        unset($config['desc']);

        return new ContextMeta([
            'name' => $this->name,
            'title' => $this->title,
            'desc' => $this->desc,
            'wrapper' => static::class,
            'config' => $config
        ]);
    }


    /**
     * @param Meta $meta
     * @return Wrapper
     */
    public static function wrapMeta(Meta $meta): Wrapper
    {
        if (!$meta instanceof ContextMeta) {
            throw new InvalidArgumentException(
                __METHOD__
                . ' only accept meta of subclass ' . ContextMeta::class
            );
        }

        $config = $meta->config;
        $config['name'] = $meta->name;
        $config['title'] = $meta->title;
        $config['desc'] = $meta->desc;
        return static::create($config);
    }
}