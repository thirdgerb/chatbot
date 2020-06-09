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
class CallablePaginator
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $total;

    /**
     * CallablePaginator constructor.
     * @param callable $callable
     * @param int $limit
     * @param int $total
     */
    public function __construct(callable $callable, int $limit, int $total)
    {
        $this->callable = $callable;
        $this->limit = $limit;
        $this->total = $total;
    }


    public function count() : int
    {
        return $this->total;
    }

    public function page(int $page) : array
    {
        $page = $page > 0 ? $page : 1;
        $offset = ($page - 1) * $this->limit;

        return call_user_func($this->callable, $offset, $this->limit);
    }

    public function maxPage() : int
    {
        return ceil($this->count() / $this->limit);
    }


}