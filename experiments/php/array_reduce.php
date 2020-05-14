<?php


return array_reduce([1,2,3], function($a, $b){
    var_dump($a, $b);
    exit;
}, []);
