<?php


namespace Commune\Chatbot\App\Contexts\Restful;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

interface ResourceHelper
{
    public function toSelfResource(Dialog $dialog, $id) : Navigator;

    public function tellReturnr(Dialog $dialog, Context $resource) : void;

    public function checkResult(Dialog $dialog, string $error = null) : void;

    /**
     * @param array $path
     * @param string $key
     * @param int|string|null $id
     * @return ResourceDef
     */
    public function newResource(array $path, string $key, $id) : ResourceDef;


    /*---- 视图 ----*/

    function resourceView(Dialog $dialog) : ? Navigator;

    function listView(Dialog $dialog) : ? Navigator;
}