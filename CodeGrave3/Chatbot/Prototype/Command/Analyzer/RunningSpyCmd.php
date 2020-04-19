<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Prototype\Command\Analyser;

use Commune\Framework\Blueprint\Command\CommandMsg;
use Commune\Framework\Blueprint\Session;
use Commune\Framework\Blueprint\Session\SessionCmdPipe;
use Commune\Framework\Exceptions\InvalidClassException;
use Commune\Framework\Prototype\Command\ISessionCmd;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyAgency;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RunningSpyCmd extends ISessionCmd
{
    const SIGNATURE = 'runningSpy
        {--d|detail : 查看所有选项的详情}
    ';

    const DESCRIPTION = '查看一些关键类的实例数量. 用于排查部分内存泄露问题.';

    /**
     * @var bool
     */
    protected $silent = true;

    public function handle(CommandMsg $message, Session $session, SessionCmdPipe $pipe): void
    {
        if(! SpyAgency::$running) {
            $this->error('SpyAgency is not running');
            return;
        }

        $detail = $message['--detail'] ?? false;
        $spies = SpyAgency::getSpies();

        $str = '';

        foreach ($spies as $running) {
            if (!is_a($running, Spied::class, TRUE)) {
                throw new InvalidClassException( Spied::class, $running);
            }

            $str .= $this->showRunningTrace(
                $running,
                call_user_func([$running, 'getRunningTraces']),
                $detail
            );
        }

        $this->info($str);
    }

    protected function showRunningTrace(string $type, array $traces, bool $showDetail) : string
    {
        $c = count($traces);

        $slices = array_slice($traces, 0, 20);

        $output = "\n$type 运行中实例共 $c 个";
        if ($showDetail) {
            $output .= "\n列举最多20个如下:\n";
            foreach ($slices as $trace => $id) {
                $output .= "  $trace : $id\n";
            }
        }

        return $output;
    }



}