<?php


namespace Commune\Chatbot\OOHost\Dialogue;

use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;

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


    /*------ 基于 NLU + Intent 实现的对话. 吸收了 DuerOS 的做法. 但不完全兼容. ------*/

    /**
     * 向用户提问要求意图的一个值.
     * 会把匹配到意图的值作为答案来处理.
     * 同时会修改原来Intent的该值.
     *
     * ask value for context->{entityName}
     *
     * @param string $question
     * @param IntentMessage $intent
     * @param string $entityName
     * @param mixed $default
     * @return static
     */
    public function askIntentEntity(
        string $question,
        IntentMessage $intent,
        string $entityName,
        $default = null
    ) ;

    /**
     * 要求用户确认当前意图.
     * 确认后会返回结果.
     *
     * @param string $question
     * @param IntentMessage $intent
     * @return static
     */
    public function askConfirmIntent(string $question, IntentMessage $intent) ;

    /**
     * 向用户提问确认一个值.
     * 会根据意图匹配的结果来处理答案.
     * 如果
     *
     * @param string $question
     * @param IntentMessage $intent
     * @param string $entityName
     * @return static
     */
    public function askConfirmEntity(string $question, IntentMessage $intent, string $entityName) ;


    /**
     * 向用户提问选择多个值.
     *
     * @param string $question
     * @param IntentMessage $intent
     * @param string $entityName
     * @param array $suggestions
     * @return static
     */
    public function askSelectEntity(
        string $question,
        IntentMessage $intent,
        string $entityName,
        array $suggestions
    ) ;

    /**
     * 向用户提问选择一个值.
     *
     * @param string $question
     * @param IntentMessage $intent
     * @param string $entityName
     * @param array $suggestions
     * @return static
     */
    public function askChooseEntity(
        string $question,
        IntentMessage $intent,
        string $entityName,
        array $suggestions
    );


    /**
     * 要求用户做选择. 不过选项用意图来表述.
     *
     * @param string $question
     * @param array $options
     * @param array $intentNames
     * @param null $defaultChoice
     * @return static
     */
    public function askChooseIntents(
        string $question,
        array $options,
        array $intentNames,
        $defaultChoice = null
    );
}