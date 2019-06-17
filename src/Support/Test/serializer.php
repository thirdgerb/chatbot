<?php

class ABC
{

    protected $a  = 1;

    protected $b = 0;

    public function __construct(int $b)
    {
        $this->b = $b;
    }

    /**
     * @param int $c
     */
    public function test(int $c)
    {
        $d = 0;

        $d += $this->a;

        Serializer::make(__METHOD__, __LINE__,  get_defined_vars(), $this);

        $d += $c;
        $d += $this->b;

        return $d;
    }


    public function __get($name)
    {
        return $this->{$name};
    }

}


class Serializer {

    protected $object;

    /**
     * @var int
     */
    protected $start;

    /**
     * @var int
     */
    protected $end;

    protected $file;

    protected $vars;

    protected $bind;

    public static function make(
        string $method,
        int $start,
        array $vars,
        object $bind
    )
    {
        $obj = new self($method, $start, $vars, $bind);

        throw new SerializerException($obj);
    }

    public function __construct(
        string $method,
        int $start,
        array $vars,
        object $bind
    )
    {
        $mr = new ReflectionMethod($method);
        $this->file = $mr->getFileName();
        $this->start = $start;
        $this->end = $mr->getEndLine();
        $this->vars = $vars;
        $this->bind = $bind;
    }

    public function getClosure() : Closure
    {
        $vars = $this->vars;
        $code = $this->getCode();

        $c = function() use ($vars, $code) {
            extract($vars);
            return eval($code);
        };

        return $c->bindTo($this->bind);
    }

    protected function getCode() : string
    {
        $file = new SplFileObject($this->file);

        $start = $this->start;
        $end = $this->end - 1;
        $file->seek($start);
        $code = '';

        for($i = $start + 1; $i < $end ; $i ++ ) {
            $code .= $file->getCurrentLine();
            $file->next();
        }
        return $code;
    }

}

class SerializerException extends Exception {

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * SerializerException constructor.
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
        parent::__construct();
    }

    /**
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }
}

try {

    $g = (new ABC(3))->test(4);
    var_dump($g);
} catch (SerializerException $e) {
    var_dump(123);
    $b = $e->getSerializer()->getClosure();
    var_dump($b());
}

