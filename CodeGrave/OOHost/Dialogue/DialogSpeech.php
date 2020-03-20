<?php


namespace Commune\Chatbot\OOHost\Dialogue;

use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;

/**
 * 在 Dialog 对象中发布回复的 api
 */
interface DialogSpeech extends Speech
{

    /**
     * 增加全局的 slots
     *
     * @param array $slots
     * @return static
     */
    public function withSlots(array $slots);

    /**
     * 使用 Context 对象来填充 slots
     * 如果没有定义 $keys, 调用 $context->toAttributes() 获得具体参数
     * 如果定义了 keys, 则传入的 slots 只是 Context 对象这些 key 的值.
     *
     * @param Context|null $from
     * @param array $keys
     * @return static
     */
    public function withContext(Context $from = null, array $keys = []);


    /**
     * 直接传入一个 ReplyMsg 对象
     * @param ReplyMsg $reply
     * @return static
     */
    public function withReply(ReplyMsg $reply);

    /*------ 段落 ------*/


    /**
     * 开启段落, 多个消息会被合并成一段
     * @param string $joint
     * @return static
     */
    public function beginParagraph(string $joint = '');

    /**
     * 结束一个段落
     * @return static
     */
    public function endParagraph();


    /*------ 默认的问题. ------*/

    /**
     * 用自己封装的 Question 对象提问
     * @param Question $question
     * @return static
     */
    public function ask(Question $question);

    /**
     * 要求一个口头的, 或者文字类型的回答.
     *
     * 通常搭配 Hearing::isInstanceOf(Verbal::class) 使用
     *
     * @param string $question 默认的问题.
     * @param array $suggestions 给出的建议.
     * @return static
     */
    public function askVerbal(
        string $question,
        array $suggestions = []
    );

    /**
     * 要求用户在给出的建议中选择一个值.
     * 选择的方式, 可以是对索引的唯一匹配, 或者对值的唯一匹配.
     *
     * 通常搭配 Hearing::isChoice 使用
     *
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
     * 要求用户对一个问题表达确认或否认.
     *
     *
     * @param string $question
     * @param bool $default
     * @param string|null $yes
     * @param string|null $no
     * @return static
     */
    public function askConfirm(
        string $question,
        bool $default = true,
        string $yes = null,
        string $no = null
    );

    /**
     * 要求用户在给出的建议中选择若干个值.
     *
     * 此功能还在评估中, 未来可能用标准的多轮对话组件取代.
     *
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