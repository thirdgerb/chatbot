<?php


namespace Commune\Chatbot\Blueprint\Conversation;

/**
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
     * bind template to id.
     * actually register concrete for id to container
     * usually process level singleton
     *
     * @param string $id
     * @param string $template  abstract of template
     * @throws \InvalidArgumentException
     */
    public function bindTemplate(string $id, string $template) : void;

    /**
     * make template from container
     *
     * @param string $id
     * @return ReplyTemplate
     */
    public function makeTemplate(string $id) : ReplyTemplate;

}