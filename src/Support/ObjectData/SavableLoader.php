<?php


namespace Commune\Support\ObjectData;


interface SavableLoader
{

    public function save(Savable $object) : void;

    public function exists(SavableIdentity $id) : bool ;

    public function find(SavableIdentity $id) : ? Savable;

}