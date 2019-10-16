<?php


namespace Commune\Chatbot\OOHost\Context\Intent;

use Commune\Chatbot\OOHost\Context\AbsContext;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionInstance;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


abstract class AbsIntent
    extends AbsContext
    implements IntentMessage, HasIdGenerator
{
    use IdGeneratorHelper;

    /**
     * @var bool|null
     */
    protected $_isConfirmed = null;

    /**
     * @var boolean[]
     */
    protected $_confirmedEntities = [];

    public function getId(): string
    {
        return $this->_contextId ?? $this->_contextId = $this->createUuId();
    }

    /**
     * intent 被 context 捕获后, 如果context 自己没有handler
     * 则默认调用intent 的navigate 方法.
     *
     * intent 默认方法会跳到自己的流程中.
     *
     * 默认是开启intent自己.
     * @param Dialog $dialog
     * @return Navigator|null
     */
    abstract public function navigate(Dialog $dialog): ? Navigator;



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
        $this->getId();
        $this->assign();
        $this->_session->repo->cacheSessionData($this);

        return $this;
    }

    public function __getIsConfirmed() : ? bool
    {
        return $this->_isConfirmed;
    }

    public function __setIsConfirmed($confirmed) : void
    {
        $this->_isConfirmed = boolval($confirmed);
    }

    public function __getConfirmedEntities() : array
    {
        return $this->_confirmedEntities;
    }

    /**
     * @param boolean[] $values
     */
    public function __setConfirmedEntities(array $values) : void
    {
        $this->_confirmedEntities = array_map(function($i){
            return boolval($i);
        }, $values);
    }

    public function __sleep(): array
    {
        $names = parent::__sleep();
        $names[] = '_isConfirmed';
        $names[] = '_confirmedEntities';
        return $names;
    }
}