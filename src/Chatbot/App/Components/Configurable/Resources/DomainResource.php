<?php


namespace Commune\Chatbot\App\Components\Configurable\Resources;


use Commune\Chatbot\App\Components\Configurable\Configs\DomainConfig;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class DomainResource extends AbsDomainResource
{

    // 资源的所在域. 必填. 没有 '.' 的字符串.
    const MODULE = 'configurable';

    // 资源对应的key, 有先后顺序, 最后一个是自己.
    const PATHS = [];

    // 资源自己 ID 的名称.
    const IDENTITY = 'domain';

    const DESCRIPTION = 'list';

    /**
     * @param DomainConfig $option
     * @return string
     */
    public function describeResource($option): string
    {
        return $option->desc;
    }

    public function getDomainResourceMenu(): array
    {
        $menu = parent::getDomainResourceMenu();
        $menu['修改 desc'] = 'changeDesc';
        $menu['修改 intents'] = [ $this, 'toIntent'];

        return $menu;
    }

    public function __onChangeDesc(Stage $stage) : Navigator
    {
        return $this->doChangeVerbose($stage, 'desc');
    }

    public function toIntent(Dialog $dialog) : Navigator
    {
        return $this->toSubList($dialog, 'intents');
    }

    function itemsView(Dialog $dialog, array $items): ? Navigator
    {
        // TODO: Implement itemsView() method.
    }


}