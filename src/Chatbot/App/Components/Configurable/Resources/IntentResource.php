<?php


namespace Commune\Chatbot\App\Components\Configurable\Resources;


use Commune\Chatbot\App\Components\Configurable\Configs\IntentConfig;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Support\Option;

class IntentResource extends AbsDomainResource
{

    // 资源的所在域. 必填. 没有 '.' 的字符串.
    const MODULE = 'configurable';

    // 资源对应的key, 有先后顺序, 最后一个是自己.
    const PATHS = ['domain'];

    // 资源自己 ID 的名称.
    const IDENTITY = 'intents';

    const DESCRIPTION = 'edit intents';

    /**
     * @param IntentConfig $option
     * @return string
     */
    public function describeResource($option): string
    {
        return $option->desc;
    }

    function itemsView(Dialog $dialog, array $items): ? Navigator
    {
        // TODO: Implement itemsView() method.
    }


}