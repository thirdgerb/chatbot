<?php


namespace Commune\Chatbot\OOHost\Context;


use Commune\Chatbot\Framework\Utils\StringUtils;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionInstance;
use Commune\Container\ContainerContract;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * 基于class, 用面向对象的方式来定义的 context
 */
abstract class OOContext
    extends AbsContext
    implements HasIdGenerator, SelfRegister
{
    use IdGeneratorHelper;

    const DESCRIPTION = 'please define description at subclass';


    /*------- default -------*/

    public function getId(): string
    {
        return $this->_contextId ?? $this->_contextId = $this->createUuId();
    }

    public function getName(): string
    {
        return static::getContextName();
    }

    /**
     * @param Session $session
     * @return static
     */
    public function toInstance(Session $session): SessionInstance
    {
        if (isset($this->_session)) {
            return $this;
        }
        $this->_session = $session;
        $this->assign();
        $this->_session->repo->cacheSessionData($this);
        return $this;
    }


    /*------- context definition -------*/


    public function getDef(): Definition
    {
        $registrar = $this->getSession()->contextRepo;
        $name = $this->getName();

        if (!$registrar->hasDef($name)) {
            $registrar->registerDef(static::buildDefinition());
        }
        return $registrar->getDef($name);
    }


    public static function getContextName() : string
    {
        return StringUtils::normalizeContextName(static::class);
    }

    protected static function buildDefinition(): Definition
    {
        // 不可能抛出的异常
        $def = new ContextDefinition(
            static::getContextName(),
            static::class,
            static::DESCRIPTION
        );
        return $def;
    }

    public static function registerSelfDefinition(ContainerContract $processContainer): void
    {
        $processContainer
            ->get(ContextRegistrar::class)
            ->registerDef(static::buildDefinition());
    }


}