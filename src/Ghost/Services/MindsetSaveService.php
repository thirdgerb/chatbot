<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Services;

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Ghost\MindMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Ghost\Services\Payload\MindSavePayload;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MindsetSaveService extends AbsDialogicService
{
    /**
     * @var Mindset
     */
    protected $mindset;

    protected $auth = [
        Supervise::class,
    ];

    /**
     * MindsetSaveService constructor.
     * @param Mindset $mindset
     */
    public function __construct(Mindset $mindset)
    {
        $this->mindset = $mindset;
    }


    /**
     * @param array $payload
     * @see MindSavePayload
     *
     * @param Deliver $deliver
     */
    public function handle(array $payload, Deliver $deliver): void
    {
        $option = new MindSavePayload($payload);
        $metaName = $option->metaName;
        $metaData = $option->metaData;
        $force = $option->force ?? true;

        $meta = call_user_func(
            [$metaName, MindMeta\DefMeta::FUNC_CREATE],
            $metaData
        );

        $success = $this->saveMeta($meta, $force);

        if ($success) {
            $deliver->info("save $metaName success!");
        } else {
            $deliver->notice("save $metaName failed!");
        }
    }

    protected function saveMeta(MindMeta\DefMeta $meta, bool $force) : bool
    {
        $reg = $this->mindset->getRegistry($meta);
        return $reg->registerDef($meta->toWrapper(), !$force);
    }




}