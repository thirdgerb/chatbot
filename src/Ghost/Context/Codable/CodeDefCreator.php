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

use Commune\Blueprint\Ghost\Context\EntityBuilder;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Ghost\Context\Builders\IParamBuilder;
use Commune\Blueprint\Ghost\MindDef\ParamDefCollection;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CodeDefCreator
{
    /**
     * @var string
     */
    protected $contextClass;

    /**
     * CodeDefCreator constructor.
     * @param string $contextClass
     */
    public function __construct(string $contextClass)
    {
        $this->contextClass = $contextClass;
    }

    protected function isInstance(string $type) : bool
    {
        return is_a(
            $this->contextClass,
            $type,
            TRUE
        );
    }

    public function getQueryParams() : ParamDefCollection
    {
        $builder = new IParamBuilder([]);
        if ($this->isInstance( DefineQuery::class)) {
            $builder = call_user_func(
                [$this->contextClass, DefineQuery::DEFINE_QUERY_FUNC],
                $builder
            );
        }
        return $builder;
    }

    public function getConfig() : array
    {
        if ($this->isInstance(DefineConfig::class)) {
            return call_user_func(
                [$this->contextClass, DefineConfig::DEFINE_CONFIG_FUNC]
            );
        }

        return [];
    }


    /**
     * @return StageDef[]
     */
    public function getMethodStages() : array
    {

    }

    public function getParams() : ParamDefCollection
    {

    }

    public function getEntityBuilder(string $contextName) : EntityBuilder
    {

    }


}