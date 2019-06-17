<?php


namespace Commune\Support\ObjectData;


interface Savable
{
    public function getId() : string;

    public function getSavableType() : string;

    public function shouldSave() : bool ;

    public function toIdentity() : SavableIdentity;

}