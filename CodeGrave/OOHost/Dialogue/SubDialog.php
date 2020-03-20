<?php

/**
 * Class SubDialog
 * @package Commune\Chatbot\OOHost\Dialogue
 */

namespace Commune\Chatbot\OOHost\Dialogue;


use Commune\Chatbot\OOHost\Directing\Navigator;

interface SubDialog extends Dialog
{

    public function getParent() : Dialog;

    /**
     * @param callable $caller
     */
    public function onQuit(callable $caller) : void;

    public function fireQuit() : ? Navigator;


    /**
     * @param callable $caller
     */
    public function onMiss(callable $caller) : void;

    public function fireMiss() : ? Navigator;

    /**
     * @param callable $caller
     */
    public function onWait(callable $caller) : void;

    public function fireWait() : ? Navigator;


}