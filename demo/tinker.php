<?php

// 基于 php 协程实现的简单命令行工具. 用于测试一些代码.

require __DIR__ .'/../vendor/autoload.php';

use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;

$loop = Factory::create();
$stdio = new Stdio($loop);

$stdio->setPrompt('> ');


/**
 * @param callable $output
 * @param callable[] $commands
 * @return Generator
 */
function tinker(callable $output, array $commands) : Generator {

    $codeBuffer = '';
    $buffering = false;

    $i = 0;
    while (true) {
        $i ++ ;

        $input = yield $i;
        $input = trim($input);

        if (array_key_exists($input, $commands)) {
            $caller = $commands[$input];
            $caller();
            continue;
        }

        if ($input === '>>>') {
            $buffering = true;
            continue;
        }

        if ($input === '<<<') {
            $buffering = false;
            $input = $codeBuffer;
            $codeBuffer = '';
            ob_start();
            eval($input);
            $content = ob_get_clean();
            $output($content);
            continue;
        }

        if ($buffering) {
            $codeBuffer .= "\n$input";
            continue;
        }

        ob_start();
        eval($input);
        $content = ob_get_clean();
        $output($content);
    }

    yield $i;
}

$output = function(string $content) use ($stdio) {
    $lines = explode("\n", $content);
    foreach ($lines as $line) {
        $stdio->write($line);
    }
};

$quit = function() use ($stdio){
    $stdio->end('quit');
};

$gen = tinker($output, ['quit' => $quit]);

$stdio->on('data', function($line) use ($gen) {
    $gen->send($line);
});

$loop->run();