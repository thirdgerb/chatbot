<?php


class A implements ArrayAccess
{
    protected $data;

    /**
     * A constructor.
     * @param $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }


    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

}

$obj = new A([]);

// 不能这样赋值.
//$a['a']['b']['c'] = 1;
//var_dump($a['a']['b']['c']);


$obj = new A(['a' => []]);
$arr = $obj['a'];
$arr['b']['c'] = 1;
$obj['a'] = $arr;

var_dump($obj['a']);
var_dump($obj['a']);
$a = microtime(true);
$t = $obj['a'];
$b = microtime(true);
var_dump(round(($b - $a) * 1000000));
exit;

