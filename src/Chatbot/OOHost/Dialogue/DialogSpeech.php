<?php


namespace Commune\Chatbot\OOHost\Dialogue;

use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\OOHost\Context\Context;

interface DialogSpeech
{

    /**
     * @param array $slots
     * @return static
     */
    public function withSlots(array $slots);

    /**
     * 使用 slots, 在接下来的发言中会共享这些slots
     *
     * slots 可以用 key => value 的形式
     * 也可以只用 key, 此时会默认从数据源 $from 里获取.
     *
     * $from 为 null 的时候, 认为是当前的 context自身.
     *
     * @param Context|null $from
     * @param array $keys
     * @return static
     */
    public function withContext(Context $from = null, array $keys = []);

    /*------ implements speech --------*/

    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function debug(string $message, array $slots = []);


    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function info(string $message, array $slots = []);


    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function warning(string $message, array $slots = []);


    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function notice(string $message, array $slots = []);


    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function error(string $message, array $slots = []);


    public function trans(string $id, array $slots = []) : string;

    /*------ 默认的问题. ------*/

    /**
     * @param Question $question
     * @return static
     */
    public function ask(Question $question);

    /**
     * @param string $question
     * @param array $suggestions
     * @return static
     */
    public function askVerbose(
        string $question,
        array $suggestions = []
    );

    /**
     * @param string $question
     * @param array $suggestions
     * @param int|string|null $default
     * @return static
     */
    public function askChoose(
        string $question,
        array $suggestions,
        $default = null
    );

    /**
     * @param string $question
     * @param array $suggestions
     * @param string|null $default
     * @return static
     */
    public function askSelects(
        string $question,
        array $suggestions,
        string $default = null
    );
//
//    public function askMessageTypes(
//        string $question,
//        array $allowedTypes
//    ) : Talk;

    /**
     * @param string $question
     * @param bool $default
     * @param string $yes
     * @param string $no
     * @return static
     */
    public function askConfirm(
        string $question,
        bool $default = true,
        string $yes = 'y',
        string $no = 'n'
    );




}