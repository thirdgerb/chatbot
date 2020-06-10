<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Codable;

use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Stage\AttrStageDef;
use Commune\Ghost\Stage\DependStageDef;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Struct\Struct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IDepending implements Depending
{
    /**
     * @var string
     */
    public $contextName;

    /**
     * @var StageMeta[]
     */
    public $stages = [];

    /**
     * @var array
     */
    public $attrs = [];

    /**
     * IDepending constructor.
     * @param string $contextName
     */
    public function __construct(string $contextName)
    {
        $this->contextName = $contextName;
    }

    public function onStage(
        string $name,
        StageMeta $meta
    ): Depending
    {
        $this->attrs[$name]= null;

        $metaArr = $meta->toArray();
        $fullname = ContextUtils::makeFullStageName($this->contextName, $name);
        $metaArr['name'] = $fullname;
        $metaArr['contextName'] = $this->contextName;
        return call_user_func(
            [get_class($meta), Struct::CREATE_FUNC],
            $metaArr
        );
    }


    public function on(
        string $name,
        string $query = '',
        string $validator = null
    ): Depending
    {
        $this->attrs[$name] = null;

        $fullname = ContextUtils::makeFullStageName($this->contextName, $name);
        $this->stages[$name] = new AttrStageDef([
            'name' => $fullname,
            'contextName' => $this->contextName,
            'title' => '',
            'desc' => '',
            'query' => $query,
            'validator' => $validator,
            'stageName' => $name,
            'asIntent' => [],
            'events' => [],
            'ifRedirect' => null,
        ]);

        return $this;
    }

    public function onContext(
        string $name,
        Ucl $ucl,
        string $validator = null
    ): Depending
    {
        $this->attrs[$name] = null;

        $fullname = ContextUtils::makeFullStageName($this->contextName, $name);
        $this->stages[$name] = (new DependStageDef([
            'name' => $fullname,
            'contextName' => $this->contextName,

            'dependedUcl' => $ucl->encode(),
            'dependedAttr' => null,
            'validator' => $validator,

            'stageName' => $name,
            'asIntent' => [],
            'events' => [],
            'ifRedirect' => null,
            'title' => '',
            'desc' => '',
        ]))->toMeta();

        return $this;
    }

    public function onContextAttr(
        string $name,
        Ucl $ucl,
        string $attrName,
        string $validator = null
    ): Depending
    {
        $this->attrs[$name] = null;

        $fullname = ContextUtils::makeFullStageName($this->contextName, $name);
        $this->stages[$name] = (new DependStageDef([
            'name' => $fullname,
            'contextName' => $this->contextName,

            'dependedUcl' => $ucl->encode(),
            'dependedAttr' => $attrName,
            'validator' => $validator,

            'stageName' => $name,
            'asIntent' => [],
            'events' => [],
            'ifRedirect' => null,
            'title' => '',
            'desc' => '',
        ]))->toMeta();

        return $this;
    }


}