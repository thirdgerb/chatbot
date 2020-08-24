<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Paginator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface PaginatorInterface
{

    public function getPage() : int;

    public function getLimit() : int;

    public function getTotal() : int;

    public function getTotalPage() : int;

    public function getList() : array;

    public function prev() : ? PaginatorInterface;

    public function next() : ? PaginatorInterface;

    public function first() : PaginatorInterface;

    public function to(int $page) : PaginatorInterface;

    public function last() : PaginatorInterface;

}