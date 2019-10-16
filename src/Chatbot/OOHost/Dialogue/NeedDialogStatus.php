<?php


namespace Commune\Chatbot\OOHost\Dialogue;


/**
 * 用在 MessageRequest 上.
 * 每次 wait 时会检查.
 */
interface NeedDialogStatus
{
    /**
     * @param Dialog $dialog
     */
    public function logDialogStatus(Dialog $dialog) : void;

}