<?php


namespace Commune\Chatbot\OOHost\Context\Memory;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\AbsContext;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Registrar;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Exceptions\DataNotFoundException;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionInstance;

abstract class AbsMemory extends AbsContext implements Memory
{

    public function getId(): string
    {
        if (isset($this->_contextId)) {
            return $this->_contextId;
        }

        $this->hasInstanced();
        return $this->_contextId = $this->_session
            ->scope
            ->makeScopingId($this->getName(), $this->getScopingTypes());
    }



    /**
     * @param Session $session
     * @return static
     */
    public function toInstance(Session $session): SessionInstance
    {
        // 一个实例不执行两次.
        if ($this->isInstanced()) {
            return $this;
        }

        $this->_session = $session;
        $this->getId();

        /**
         * 注意, Memory 自己就是一个占位符.
         * 如果session 有保存, 会优先用保存的实例.
         *
         * 如果session 没有保存, 则会使用自己做实例.
         *
         * 由于已经 set session 了, 所以都不会第二次调用.
         *
         * @var AbsMemory $data
         */
        $data = $session->repo->fetchSessionData(
            $identity = $this->toSessionIdentity(),
            function() {
                $this->assign();
                return $this;
            }
        );

        if (!isset($data) || !$data instanceof self) {
            throw new DataNotFoundException($identity);
        }

        return $data;
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->dialog->fulfill();
    }

    public function __exiting(Exiting $listener): void
    {
    }

    /**
     * @return MemoryRegistrar
     */
    protected static function getRegistrar() : Registrar
    {
        return MemoryRegistrar::getIns();
    }

    public function namesAsDependency(): array
    {
        return [
            Memory::class,
            AbsMemory::class,
            static::class
        ];
    }

}