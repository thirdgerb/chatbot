<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Builders;

use Commune\Blueprint\Ghost\Context\ParamBuilder;
use Commune\Blueprint\Ghost\MindDef\ParamDefCollection;
use Commune\Blueprint\Ghost\MindMeta\Option\ParamOption;
use Commune\Ghost\Context\IParamDefCollection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IParamBuilder implements ParamBuilder
{

    /**
     * @var ParamOption[]
     */
    protected $options = [];

    /**
     * IParamBuilder constructor.
     * @param ParamOption[] $options
     */
    public function __construct(array $options)
    {
        foreach ($options as $option) {
            $this->options[$option->getId()] = $option;
        }
    }

    public function define(
        string $name,
        $default = null,
        $type = null,
        $parser = null
    ): ParamBuilder
    {
        $option = new ParamOption([
            'name' => $name,
            'default' => $default,
            'type' => $type,
            'parser' => $parser
        ]);

        $this->options[$option->getId()] = $option;

        return $this;
    }

    public function getParams(): ParamDefCollection
    {
        return new IParamDefCollection($this->options);
    }



}