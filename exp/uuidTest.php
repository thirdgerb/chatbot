<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

require_once __DIR__ . '/../vendor/autoload.php';

class A {
    use \Commune\Support\Uuid\IdGeneratorHelper;

}

function order(string $new, string $id)  : bool{
    $len = strlen($new);
    for($i = 0; $i < $len; $i ++ ) {
        if (ord($id[$i]) > ord($new[$i])) {
            return false;
        }
    }
    return true;
}

$aa = new A();

$id = $aa->createUuId();
$a = microtime(true);

for($i = 0; $i < 10000; $i ++ ) {
    $new = $aa->createUuId();
    if (order($new, $id)) {
        echo "fail : $id, $new\n";
        exit(1);
    }

    $id = $new;

}
$b = microtime(true);
var_dump(round(($b - $a) * 1000000));
exit;





