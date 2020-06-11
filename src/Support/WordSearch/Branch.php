<?php


namespace Commune\Support\WordSearch;


class Branch
{

    /**
     * 当前节点的子节点.
     * @var Branch[]
     */
    protected $branches = [];

    /**
     * 当前节点的字符
     * @var string
     */
    protected $character = '';

    /**
     * 当前节点是否为最终节点.
     * @var mixed|null
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
        // 如果没有值, 为何还要匹配
        if ($value === '') {
            return;
        }

        // 检查第一个字符.
        $first = mb_substr($key, 0, 1);

        // 第一个字符为空时, 当前节点是叶子节点.
        if ($first === '') {
            $this->value = $value;
            return;
        }

        // 如果还没添加过, 则添加子节点.
        if (!isset($this->branches[$first])) {
            $this->branches[$first] = new Branch($first);
        }

        $branch = $this->branches[$first];
        // 子节点创建新的分支.
        $next = mb_substr($key, 1);
        $branch->buildBranches($next, $value);
    }


    /**
     * 返回值是新的 start
     * @param string $haystack  搜索的字符串.
     * @param int $start        游标的位置
     * @param array $result     已有的搜索结果.
     * @return int              返回游标的位置.
     */
    public function search(
        string &$haystack,
        int $start,
        array &$result
    ) : ? int
    {
        $character = mb_substr($haystack, $start, 1);

        // 有值就赋值., 说明已经到了叶子节点位置.
        if (isset($this->value)) {
            $origin = $result[$this->value] ?? 0;
            $result[$this->value] = $origin + 1;
            return $start;
        }

        // 为空不继续.
        if ($character === '') {
            return null;
        }

        // 没有子节点, 放弃.
        if (!$this->hasBranch($character)) {
            return null;
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