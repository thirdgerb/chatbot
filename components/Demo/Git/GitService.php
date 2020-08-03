<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo\Git;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Ghost\Callables\DialogicService;
use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Message\Host\Convo\Verbal\MarkdownMsg;
use Commune\Support\Swoole\SwooleUtils;
use Swoole\Coroutine;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GitService implements DialogicService
{

    /**
     * @param array $payload  [
     *    'command' => 'name'
     * ]
     * @param Deliver $deliver
     */
    public function __invoke(array $payload, Deliver $deliver): void
    {
        $command = $payload['command'];

        if (empty($command)) {
            $deliver->error("命令参数不存在!");
            return;
        }

        $deliver->message(MarkdownMsg::instance("接受到异步命令 ```$command```"));
        $path = CommuneEnv::getBasePath();
        if ($command === 'status') {
            $this->run($deliver, $path, "git status");
            return;
        }

        if ($command === 'state') {
            $this->run($deliver, $path, "git log --pretty=tformat: --numstat  -- $@ | awk '{ add += $1 ; subs += $2 ; loc += $1 + $2 } END { printf \"added lines: %s; removed lines : %s; total lines: %s\", add,subs,loc }' -");
            return;
        }

        $deliver->notice("当前命令 $command 不支持!");
    }

    protected function run(Deliver $deliver, string $path, string $command) : void
    {
        // 故意制造延时的效果.
        if (SwooleUtils::isInCoroutine()) {
            Coroutine::sleep(2);
        }

        exec("cd $path && $command", $output, $code);

        $output = implode("\n", $output);
        if ($code === 0) {
            $deliver
                ->message(MarkdownMsg::instance("运行命令 ```$command```, 结果是:"))
                ->message(MarkdownMsg::code($output))
                ->over();
            return;
        } else {
            $deliver
                ->error("运行异常")
                ->error($output)
                ->over();
        }
    }
}