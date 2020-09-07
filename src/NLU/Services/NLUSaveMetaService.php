<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\Services;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Blueprint\NLU\NLUManager;
use Commune\Ghost\Predefined\Services\AbsDialogicService;
use Commune\Ghost\Predefined\Services\Payload\MindSavePayload;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NLUSaveMetaService extends AbsDialogicService
{

    /**
     * @var NLUManager
     */
    protected $manager;

    /**
     * @var Cloner
     */
    protected $cloner;

    protected $auth = [
        // Supervise::class,
    ];

    /**
     * NLUSaveMetaService constructor.
     * @param NLUManager $manager
     * @param Cloner $cloner
     */
    public function __construct(NLUManager $manager, Cloner $cloner)
    {
        $this->manager = $manager;
        $this->cloner = $cloner;
    }


    public function handle(array $payload, Deliver $deliver): void
    {
        $saving = new MindSavePayload($payload);
        $meta = $saving->toMeta();
        $info = $this->manager->saveMeta($this->cloner, $meta);

        if (!empty($info)) {
            $deliver->error($info);
        } else {
            $deliver->info(__METHOD__ . ' success');
        }
    }


}