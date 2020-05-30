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

use Commune\Blueprint\Ghost\Context\ContextOption;
use Commune\Blueprint\Ghost\Context\EntityBuilder;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Ghost\Context\Builders\IParamBuilder;
use Commune\Support\Utils\StringUtils;


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

    public function getContextName() : string
    {
        return call_user_func([
            $this->contextClass,
            CodeContext::CONTEXT_NAME_FUNC
        ]);
    }

    public function getCodeContextOption() : CodeContextOption
    {
        return call_user_func([
            $this->contextClass,
            CodeContext::CONTEXT_OPTION_FUNC
        ]);
    }

    public function getContextIntentInfo() : array
    {
        $r = new \ReflectionClass($this->contextClass);
        return $this->readIntentInfoByComment($r->getDocComment());
    }


    protected function readIntentInfoByComment(string $doc) : array
    {
        $intentName = StringUtils::fetchAnnotation($doc, 'intent')[0] ?? '';
        $signature = StringUtils::fetchAnnotation($doc, 'signature')[0] ?? '';
        $examples  = StringUtils::fetchAnnotation($doc, 'example');
        $regex = StringUtils::fetchAnnotation($doc, 'regex');

        return [
            'examples' => $examples,
            'alias' => $intentName,
            'signature' => $signature,
            'regex' => $regex
        ];
    }

    public function getPredefinedStageMetas() : array
    {
        $r = new \ReflectionClass($this->contextClass);
        $results = [];
        foreach ($r->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $isStageMethod = strpos(
                $method->getName(),
                CodeContext::STAGE_BUILDER_PREFIX
            ) === 0;

            $name = substr($method->getName(), strlen(CodeContext::STAGE_BUILDER_PREFIX));

            if ($isStageMethod) {
                $results[$name] = $this->buildStageMeta($name, $method);
            }
        }

        return $results;
    }

    protected function buildStageMeta(string $name, \ReflectionMethod $method) : StageMeta
    {

        return new ICodeStageDef([

        ]);
    }



}