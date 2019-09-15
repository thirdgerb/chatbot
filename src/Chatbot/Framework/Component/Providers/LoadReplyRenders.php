<?php

/**
 * Class LoadReplyRenders
 * @package Commune\Chatbot\Framework\Component\Providers
 */

namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Blueprint\Conversation\Renderer;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;

class LoadReplyRenders extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;


    /**
     * @var array
     */
    protected $renders;


    /**
     * @var bool
     */
    protected $force;


    /**
     * LoadReplyRenders constructor.
     * @param ContainerContract $app
     * @param array $renders
     * @param bool $force
     */
    public function __construct(ContainerContract $app, array $renders, bool $force)
    {
        $this->renders = $renders;
        $this->force = $force;
        parent::__construct($app);
    }

    public function boot($app)
    {
        /**
         * @var Renderer $renderer
         */
        $renderer = $app->get(Renderer::class);
        foreach ($this->renders as $replyId => $template) {
            $renderer->bindTemplate($replyId, $template, $this->force);
        }

    }

    public function register()
    {
    }


}