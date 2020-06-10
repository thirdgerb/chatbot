<?php


namespace Commune\Support\WordSearch;


class Tree
{
    /**
     * @var Branch
     */
    protected $root;

    /**
     * Tree constructor.
     *
     * string => string
     * search key => final value
     * @param array $matcher
     */
    public function __construct(array $matcher)
    {
        $this->root = new Branch('');
        foreach ($matcher as $key => $value) {
            if ($key === '') {
                continue;
            }
            $this->root->buildBranches(strval($key), $value);
        }
    }

    /**
     * @param string $text
     * @return int[]  string $final => int  值 => 出现次数
     */
    public function search(string $text) : array
    {
        // 空值不搜索.
        if ($text === '') {
            return [];
        }

        $length = mb_strlen($text);
        $result = [];
        $i = 0 ;

        while ($i < $length) {
            $i = $this->root->search($text, $i, $result);
            $i ++;
        }

        return $result;
    }

}