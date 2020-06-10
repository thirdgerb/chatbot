<?php


namespace Commune\Support\WordSearch;


class Branch
{

    /**
     * @var Branch[]
     */
    protected $branches = [];

    /**
     * @var string
     */
    protected $character = '';

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Branch constructor.
     * @param string $character
     */
    public function __construct(string $character)
    {
        $this->character = $character;
    }

    public function buildBranches(string $key, $value) : void
    {
        if ($value === '') {
            return;
        }

        $first = mb_substr($key, 0, 1);

        if ($first === '') {
            $this->value = $value;
            return;
        }

        if (!isset($this->branches[$first])) {
            $this->branches[$first] = new Branch($first);
        }

        $branch = $this->branches[$first];
        $next = mb_substr($key, 1);
        $branch->buildBranches($next, $value);
    }


    /**
     * 返回值是新的 start
     * @param string $haystack
     * @param int $start
     * @param array $result
     * @return int
     */
    public function search(
        string &$haystack,
        int $start,
        array &$result
    ) : int
    {
        $character = mb_substr($haystack, $start, 1);

        // 有值就赋值.
        if (isset($this->value)) {
            $origin = $result[$this->value] ?? 0;
            $result[$this->value] = $origin + 1;
        }

        // 为空不继续.
        if ($character === '') {
            return $start;
        }

        // 没有子节点, 放弃.
        if (!$this->hasBranch($character)) {
            return $start;
        }

        $subBranch = $this->branches[$character];
        return $subBranch->search($haystack, $start + 1, $result);
    }

    public function hasBranch(string $key) : bool
    {
        return array_key_exists($key, $this->branches);
    }

    public function getBranch(string $key) : ? Branch
    {
        return $this->branches[$key] ?? null;
    }

    public function hasValue() : bool
    {
        return isset($this->value);
    }
}