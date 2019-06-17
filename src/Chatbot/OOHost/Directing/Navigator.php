<?php


namespace Commune\Chatbot\OOHost\Directing;


interface Navigator
{
    public function display() : ? Navigator;
}