<?php


namespace Commune\Chatbot\App\Commands\Analysis;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Chatbot\OOHost\Session\Session;
use Illuminate\Support\Collection;

class ContextRepoCmd extends SessionCommand
{
    const SIGNATURE = 'contextRepo
    {domain? : 命名空间, 默认为空, 表示所有}
    {page? : 查看第几页, 默认为0}
    {limit? : 每页多少条, 默认为0, 表示所有}
    {--i|intent : 仓库为intent}
    {--m|memory : 仓库为memory}
    {--t|tag : 不按domain查询,转为按tag查询.常见tag如"manager"}
';

    const DESCRIPTION = '查看已注册的 context';

    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        $domain = $message['domain'] ?? '';

        $page = intval($message['page']);
        $page = $page > 0 ? $page : 0;

        $limit = intval($message['limit']);
        $limit = $limit > 0 ? $limit : 0;

        if ($message['--memory']) {
            $repo = MemoryRegistrar::getIns();
            $type = 'memory';

        } elseif($message['--intent']) {
            $repo = IntentRegistrar::getIns();
            $type = 'intent';

        } else {
            $repo = ContextRegistrar::getIns();
            $type = 'context';

        }

        if ($message['--tag']) {
            $names = $repo->getNamesByTag($domain);
        } else {
            $names = $repo->getNamesByDomain($domain);
        }


        if ($limit > 0 ) {
            $names = (new Collection($names))->splice($page * $limit, $limit);
        }

        $result = [];

        foreach ($names as $name) {
            $desc = $repo->get($name)->getDesc();
            $result[] = "$name : $desc";
        }

        $this->say()
            ->info("已注册的 $type 为: ")
            ->info(implode("\n", $result));
    }


}