<?php


namespace Commune\Chatbot\Framework\Conversation;

use Commune\Chatbot\Blueprint\Conversation\Renderer;
use Commune\Chatbot\Blueprint\Conversation\ReplyTemplate;
use Commune\Container\ContainerContract;

class RendererImpl implements Renderer
{
    // prevent conflict id in container
    const ID_PREFIX = 'render.temp:';

    /**
     * Process Container
     * @var ContainerContract
     */
    protected $container;

    /**
     * @var callable[]
     */
    protected $templates = [];

    /**
     * RendererImpl constructor.
     * @param ContainerContract $container
     */
    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
    }


    /**
     * has template
     *
     * @param string $id
     * @return bool
     */
    public function boundTemplate(string $id): bool
    {
        return array_key_exists($id, $this->templates);
    }

    public function bindTemplate(string $id, string $template, bool $force = false): void
    {
        $this->templates[$id] = $template;

        // has bound
        if (!$force && $this->container->bound($template)) {
            return;
        }


        $parent = ReplyTemplate::class;
        if (is_a($template, $parent, TRUE)) {
            $this->container->singleton($template, $template);
            return;
        }

        throw new \InvalidArgumentException(
            "template $template has not been bound to container, thus only subclass of $parent will be bound to container as singleton"
        );
    }


    public function makeTemplate(string $id): ReplyTemplate
    {
        if (!isset($this->templates[$id])) {
            throw new \InvalidArgumentException("template $id not bound!");
        }
        $boundId = $this->templates[$id];
        return $this->container->make($boundId);
    }


}