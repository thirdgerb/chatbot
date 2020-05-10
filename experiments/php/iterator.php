<?php


class It implements IteratorAggregate
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getIterator()
    {
        foreach ($this->data as $key => $val) {
            yield $val;
        }
    }

}

$i = new It([1,2,3]);

foreach($i as $key => $val) {
    var_dump("key:$key;val:$val");
}
