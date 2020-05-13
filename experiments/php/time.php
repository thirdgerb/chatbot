<?php


$a = microtime(true);

for($i = 0; $i < 10000 ; $i ++) {
    time();
}

$b = microtime(true);
var_dump(round(($b - $a) * 1000000));
exit;

