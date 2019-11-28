<?php


namespace Commune\Chatbot\Blueprint\Conversation;

/**
 * 将回复消息通过模板 ReplyTemplate 进行渲染, 得到真正要回复的消息.
 *
 * 
 * render reply message to real outgoing messages
 * usually registered in process container
 */
interface Renderer
{
    const DEFAULT_ID = 'default';

    /**
     * has template named $id
     * @param string $id
     * @return bool
     */
    public function boundTemplate(string $id) : bool;

    /**
     * 将模板绑定到ID上. 可以通过 force 参数决定是否覆盖原来的绑定.
     *
     * bind template to id.
     * actually register concrete for id to container
     * usually process level singleton
     *
     * @param string $id  replyId
     * @param string $template  abstract of template
     * @param bool $force
     * @throws \InvalidArgumentException
     */
    public function bindTemplate(string $id, string $template, bool $force = false) : void;

    /**
     * make template from container
     *
     * @param string $id
     * @return ReplyTemplate
     */
    public function makeTemplate(string $id) : ReplyTemplate;

}