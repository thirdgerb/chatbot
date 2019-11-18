<?php


namespace Commune\Chatbot\App\Commands\Analysis;


use Commune\Chatbot\Blueprint\Message\Transformed\CommandMsg;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
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
    {--p|placeholder : 只查看 placeholder 的情况}
';

    const DESCRIPTION = '查看已注册的 context';

    protected $maxLimit = 20;

    public function handle(CommandMsg $message, Session $session, SessionCommandPipe $pipe): void
    {
        $domain = $message['domain'] ?? '';

        $page = intval($message['page']);
        $page = $page > 0 ? $page : 1;

        $limit = intval($message['limit']);
        $limit = $limit > 0 ? $limit : 0;

        if ($message['--memory']) {
            $repo = $session->memoryRepo;
            $type = 'memory';

        } elseif($message['--intent']) {
            $repo = $session->intentRepo;
            $type = 'intent';

        } else {
            $repo = $session->contextRepo;
            $type = 'context';

        }

        // 参数互斥.
        if ($message['--tag']) {
            $type .= ' of tag';
            $names = $repo->getDefNamesByTag($domain);

        } elseif ($message['--placeholder']) {
            $type .= ' of placeholder';
            $names=  $repo->getPlaceholderDefNames();

        } else {
            $type .= ' of domain';
            $names = $repo->getDefNamesByDomain($domain);
        }
        $total = count($names);
        $limit = $limit > 0 && $limit < $this->maxLimit ? $limit : $this->maxLimit;
        $totalPage = ceil($total / $limit);


        $names = (new Collection($names))->splice(($page - 1) * $limit, $limit);

        $result = [];

        $names->sort();

        foreach ($names as $name) {
            $desc = $repo->getDef($name)->getDesc();
            $result[] = "$name : $desc";
        }

        $this->say()
            ->info("已注册 domain 为 $domain 的 $type (共 $total 个, 第 $page / $totalPage 页) 为: ")
            ->info(implode("\n", $result));
    }


}