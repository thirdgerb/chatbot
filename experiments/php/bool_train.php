<?php

function boolA() : bool{
    echo 'a';
    return true;
}

function boolB() : bool {
    echo 'b';
    return false;
}

function boolC() : bool {
    echo 'c';
    return true;
}


var_dump(boolA() && boolB() && boolC());
