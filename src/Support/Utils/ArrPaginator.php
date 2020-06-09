<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Utils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrPaginator
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var int
     */
    protected $limit;

    /**
     * ArrPaginator constructor.
     * @param array $items
     * @param int $limit
     */
    public function __construct(array $items, int $limit)
    {
        $this->items = $items;
        $this->limit = $limit > 0 ? $limit : 0;
    }

    public function count() : int
    {
        return count($this->items);
    }

    public function page(int $page) : array
    {
        $page = $page > 0 ? $page : 1;
        $offset = ($page - 1) * $this->limit;
        return array_slice($this->items, $offset, $this->limit);
    }

    public function maxPage() : int
    {
        return ceil($this->count() / $this->limit);
    }



}