<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Session;
use Commune\Message\Blueprint\QuestionMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShlSessionStorage
{
    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name);

    public function set(string $name, $value) : void;

    public function setAll(array $values) : void;

    public function getAll() : array;

    public function setQuestion(QuestionMsg $question) : void;

    public function getQuestion() : ? QuestionMsg;

    public function save() : void;

}