<?php


namespace Commune\Components\Rasa\Services;


use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\Contracts\ClientFactory;
use Commune\Chatbot\OOHost\NLU\Contracts\NLUService;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Components\Rasa\RasaComponent;
use GuzzleHttp\Client;

class RasaService implements NLUService
{
    /**
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @var RasaComponent
     */
    protected $config;

    /**
     * RasaService constructor.
     * @param ClientFactory $clientFactory
     * @param RasaComponent $config
     */
    public function __construct(ClientFactory $clientFactory, RasaComponent $config)
    {
        $this->clientFactory = $clientFactory;
        $this->config = $config;
    }


    public function messageCouldHandle(Message $message): bool
    {
        if (!$message instanceof VerbalMsg) {
            return false;
        }

        $text = $message->getTrimmedText();

        // 单字符不认为有语义.
        $forbidden =  ('' === $text)
            || is_numeric($text);

        return ! $forbidden;
    }

    /*----------- request -----------*/

    public function match(Session $session): Session
    {
        try {
            $message = $session->incomingMessage->getMessage();
            $nlu = $session->nlu;

            $text = $message->getTrimmedText();
            $parsed = $this->request($text);

            $entities = $this->wrapEntities($parsed['entities'] ?? []);
            $ranking = $parsed['intent_ranking'] ?? [];

            if (empty($entities) && empty($ranking)) {
                return $session;
            }

            if (!empty($entities)) {
                $nlu->setEntities($entities);
            }

            $matchedIntent = $parsed['intent'] ?? null;
            if (!empty($matchedIntent)) {
                $possible = $this->addIntentToNLU($nlu, $matchedIntent);

                if ($possible) {
                    $nlu->setMatchedIntent($matchedIntent['name']);
                }
            }

            foreach ($ranking as $item) {
                $this->addIntentToNLU($nlu, $item);
            }

            $nlu->done(static::class);


        } catch (\Throwable $e) {
            $session->logger->error($e);
        }

        return $session;
    }

    protected function addIntentToNLU(NLU $nlu, array $item) : bool
    {
        $name = $item['name'] ?? '';
        if (empty($name)) {
            return false;
        }

        $odd = intval(($item['confidence'] ?? 0) * 100);
        $possible = $odd > $this->config->threshold;
        $nlu->addPossibleIntent($name, $odd, $possible);
        return $possible;
    }

    protected function wrapEntities(array $parsed) : array
    {
        $items = [];
        foreach ($parsed as $entity) {
            $name = $entity['entity'] ?? '';
            $value = $entity['value'] ?? '';
            // 有可能有多个值. 但多选呢? todo
            $items[$name][] = $value;
        }

        return $items;
    }


    protected function request(string $text) : ? array
    {
        $body = json_encode(['text' => $text]);

        $client = $this->makeClient();

        $option = ['body' => $body];

        $jwt = $this->config->jwt;
        if (!empty($jwt)) {
            $option['headers']['Authorization'] = "Bearer $jwt";
        }

        $result = $client->post('model/parse', $option);
        $json = $result->getBody()->getContents();
        $parsed = json_decode($json, true);

        return is_array($parsed) ? $parsed : null;
    }

    protected function makeClient() : Client
    {
        $config = $this->config->clientConfig;
        $config['base_uri'] = $this->config->server;
        return $this->clientFactory->create($config);
    }



    public function syncCorpus(Session $session): string
    {
        try {
            /**
             * @var CorpusSynchrony $synchrony
             */
            $synchrony = $session->conversation->make(CorpusSynchrony::class);
            $synchrony->outputCorpus();

        } catch (\Throwable $e) {
            return $e->getMessage();
        }

        return 'success';
    }


    public function syncIntentOption(IntentCorpusOption $option): string
    {
        return 'rasa should only sync whole corpus';
    }

    public function syncEntityDict(EntityDictOption $option): string
    {
        return 'rasa should only sync whole corpus';
    }


}