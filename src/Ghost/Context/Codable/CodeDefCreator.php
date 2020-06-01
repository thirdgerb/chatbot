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

use Commune\Blueprint\Exceptions\HostLogicException;
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
            [$this->contextClass, CodeContext::CONTEXT_NAME_FUNC]
        );
    }

    public function getCodeContextOption() : CodeContextOption
    {
        return call_user_func(
            [$this->contextClass, CodeContext::CONTEXT_OPTION_FUNC]
        );
    }

    public function getContextAnnotation() : AnnotationReflector
    {
        $r = new \ReflectionClass($this->contextClass);
        return AnnotationReflector::create($r->getDocComment());
    }

    public function getDependingBuilder() : IDepending
    {
        return call_user_func(
            [$this->contextClass, CodeContext::DEFINE_DEPENDING_FUNC],
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
            $name = substr($methodName, strlen(CodeContext::STAGE_BUILDER_PREFIX));

            if (!ContextUtils::isValidStageName($name)) {
                $class = $this->contextClass;
                throw new HostLogicException("invalid method stage name of class $class method $methodName");
            }

            $results[$name] = $this->buildStageMeta($contextName, $name, $method);
        }

        return $results;
    }

    protected function buildStageMeta(
        string $contextName,
        string $shortName,
        \ReflectionMethod $method
    ) : StageMeta
    {
        $annotation = AnnotationReflector::create($method->getDocComment());

        $def = new CodeStageDef([
            'name' => $name = ContextUtils::makeFullStageName($contextName, $shortName),
            'title' => $annotation->title,
            'desc' => $annotation->desc,

            'contextName' => $contextName,
            'stageName' => $shortName,
            'asIntent' => $annotation->asIntentMeta($name),

            'events' => [],
            'ifRedirect' => null,

        ]);

        return $def->getMeta();
    }



}