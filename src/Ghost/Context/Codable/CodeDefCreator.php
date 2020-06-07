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

use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Ghost\Context\CodeContext;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Ghost\Support\ContextUtils;

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
     * @var IDepending
     */
    protected $depending;

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
        return call_user_func(
            [$this->contextClass, CodeContext::FUNC_CONTEXT_NAME]
        );
    }

    public function getCodeContextOption() : CodeContextOption
    {
        $option = call_user_func(
            [$this->contextClass, CodeContext::FUNC_CONTEXT_OPTION]
        );

        return $option;
    }

    public function getContextAnnotation() : AnnotationReflector
    {
        $r = new \ReflectionClass($this->contextClass);
        return AnnotationReflector::create($r->getDocComment());
    }

    public function getDependingBuilder() : IDepending
    {
        return call_user_func(
            [$this->contextClass, CodeContext::FUNC_DEFINE_DEPENDING],
            new IDepending($this->getContextName())
        );
    }

    public function getMethodStageMetas() : array
    {
        $r = new \ReflectionClass($this->contextClass);
        $results = [];
        $contextName = $this->getContextName();
        foreach ($r->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $isStageMethod = strpos(
                $method->getName(),
                CodeContext::STAGE_BUILDER_PREFIX
            ) === 0;

            if (!$isStageMethod) {
                continue;
            }

            $methodName = $method->getName();
            $shortName = substr($methodName, strlen(CodeContext::STAGE_BUILDER_PREFIX));

            if (!ContextUtils::isValidStageName($shortName)) {
                $class = $this->contextClass;
                throw new CommuneLogicException("invalid method stage name of class $class method $methodName");
            }

            $results[$shortName] = $this->buildStageMeta($contextName, $shortName, $method);
        }

        return $results;
    }

    protected function buildStageMeta(
        string $contextName,
        string $shortName,
        \ReflectionMethod $method
    ) : StageMeta
    {

        $doc = $method->getDocComment();
        $doc = $doc ? $doc : '';

        $annotation = AnnotationReflector::create($doc);
        $fullName = ContextUtils::makeFullStageName($contextName, $shortName);

        $config = [
            'name' => $fullName,
            'title' => $annotation->title,
            'desc' => $annotation->desc,

            'contextName' => $contextName,
            'stageName' => $shortName,
            'asIntent' => $annotation->asIntentMeta($fullName),

            'events' => [],
            'ifRedirect' => null,

        ];
        $def = new CodeStageDef($config);
        return $def->toMeta();
    }



}