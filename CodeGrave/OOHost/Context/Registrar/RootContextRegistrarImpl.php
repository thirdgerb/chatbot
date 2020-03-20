<?php


namespace Commune\Chatbot\OOHost\Context\Registrar;


use Commune\Chatbot\OOHost\Context\Contracts\RootContextRegistrar;

class RootContextRegistrarImpl extends AbsParentContextRegistrar implements RootContextRegistrar
{
    public function getRegistrarId(): string
    {
        return RootContextRegistrar::class;
    }


}