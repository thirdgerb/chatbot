<?php


namespace Commune\Components\Rasa;


use Commune\Chatbot\OOHost\NLU\Contracts\NLULogger;
use Commune\Chatbot\OOHost\NLU\Contracts\NLUService;
use Commune\Chatbot\OOHost\NLU\Pipe\AbsNLUServicePipe;
use Commune\Components\Rasa\Services\RasaService;

class RasaSessionPipe extends AbsNLUServicePipe
{
    /**
     * @var RasaService
     */
    protected $service;


    /**
     * @var NLULogger
     */
    protected $logger;

    /**
     * RasaSessionPipe constructor.
     * @param RasaService $service
     * @param NLULogger $logger
     */
    public function __construct(RasaService $service, NLULogger $logger)
    {
        $this->service = $service;
        $this->logger = $logger;
    }

    public function getNLUService(): NLUService
    {
        return $this->service;
    }

    public function getNLULogger(): NLULogger
    {
        return $this->logger;
    }


}