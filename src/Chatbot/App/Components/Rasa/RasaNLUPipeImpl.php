<?php


namespace Commune\Chatbot\App\Components\Rasa;


use Commune\Chatbot\App\Components\RasaComponent;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Intent\Registrar;
use Commune\Chatbot\OOHost\NLU\MatchedIntent;
use Commune\Chatbot\OOHost\NLU\Matches;
use Commune\Chatbot\OOHost\NLU\NLUSessionPipe;
use Commune\Chatbot\OOHost\Session\Session;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

class RasaNLUPipeImpl extends NLUSessionPipe implements RasaNLUPipe
{
    /**
     * @var RasaComponent
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * RasaNLUPipe constructor.
     * @param RasaComponent $config
     */
    public function __construct(RasaComponent $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }


    public function messageCouldHandle(Message $message): bool
    {
        if (!$message instanceof VerboseMsg) {
            return false;
        }

        $text = $message->getTrimmedText();


        // 单字符不认为有语义.
        return !('' === $text
            || is_numeric($text)
            || preg_match('/^\w$/', $text));
    }

    protected function request(string $text) : ? array
    {
        $body = json_encode(['text' => $text]);

        $client = new Client([
            'base_uri' => $this->config->server,
        ]);

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

    public function match(Session $session) : ? Matches
    {

        $message = $session->incomingMessage->getMessage();
        $text = $message->getTrimmedText();
        $parsed = $this->request($text);

        if (!is_null($parsed)) {
            return null;
        }

        $matches = new Matches();

        $entities = $this->wrapEntities($parsed['entities'] ?? []);

        // entities 提取
        if (!$entities->isEmpty()) {
            $matches->entities = $entities;
        }

        // 意图配置
        $matchedIntents = [];
        if (isset($parsed['intent'])) {
            $matchedIntents = $this->wrapIntent(
                $matchedIntents,
                $parsed['intent'],
                $entities
            );
        }

        if (isset($parsed['intent_ranking'])) {
            $ranking = $parsed['intent_ranking'];
            foreach ($ranking as $item) {
                $matchedIntents = $this->wrapIntent(
                    $matchedIntents,
                    $parsed['intent'],
                    $entities
                );
            }
        }


        // rasa 没有关键字和分词吗? 可能目前没有作为数据返回.
        return $matches;
    }

    protected function wrapEntities(array $parsed) : Collection
    {
        $items = [];
        $itemsConfidence = [];
        foreach ($parsed as $entity) {
            $name = $entity['entity'] ?? '';
            $value = $entity['value'] ?? '';
            $confidence = $entity['confidence'] ?? 0;

            // 有可能有多个值. 但多选呢? todo
            if (!isset($itemsConfidence[$name]) || $confidence > $itemsConfidence[$name]) {
                $items[$name] = $value;
                $itemsConfidence[$name] = $confidence;
            }
        }

        return new Collection($items);
    }

    protected function wrapIntent(array $matched, $item, Collection $entities) : array
    {
        if (!is_array($item)) {
            $this->logger->warning(
                __METHOD__
                . ' receive invalid intent data which is not array: '
                . json_encode($item)
            );
            return $matched;
        }

        $name = $item['name'] ?? '';
        $odd = intval(($item['confidence'] ?? 0) * 100);

        if (empty($name) || empty($odd)) {
            $this->logger->warning(
                __METHOD__
                .' receive invalid intent data,'
                ." name:$name, odd:$odd"
            );
            return $matched;
        }

        $highlyPossible = $odd >= $this->config->threshold;

        $wrapped = new MatchedIntent(
            $name,
            $entities,
            $odd,
            $highlyPossible
        );

        $matched[] = $wrapped;
        return $matched;
    }

    public function logUnmatchedMessage(Session $session): void
    {
        // 交给别人去实现.
        return;
    }

    public function outputIntentExamples(Registrar $registrar): void
    {
        $output = '';
        $output = $this->addIntentToOutput($registrar, $output);
        $output = $this->addSynonymToOutput($output);
        $output = $this->addRegexToOutput($output);
        $output = $this->addLookUpToOutput($output);

        file_put_contents($this->config->output, $output);
    }

    protected function addIntentToOutput(Registrar $registrar, string $output) : string
    {
        $names = $registrar->getNamesByDomain('');

        foreach ($names as $name) {
            $examples = $registrar->getNLUExamplesByIntentName($name);

            if (empty($examples)) {
                continue;
            }

            $output .= "\n## intent:$name \n";
            foreach ($examples as $example) {
                // NLUExample 采用和rasa 一致的格式化.
                $output .= '- ' . $example->originText . PHP_EOL;
            }
        }

        return $output;
    }


    protected function addSynonymToOutput(string $output) : string
    {
        $synonymList = $this->config->synonym;
        foreach ($synonymList as $synonymOption) {
            $name = $synonymOption->name;
            $output .= "\n## synonym:$name\n";
            $words = implode(' ' , $synonymOption->words);
            $output .= "- $words\n";
        }

        return $output;
    }

    protected function addRegexToOutput(string $output) : string
    {
        $regex = $this->config->regex;
        foreach ($regex as $regexOption) {
            $name = $regexOption->name;
            $output .= "\n## regex:$name\n";
            foreach ($regexOption->patterns as $pattern) {
                $output .= "- $pattern\n";
            }
        }
        return $output;
    }

    protected function addLookUpToOutput(string $output) : string
    {
        $lookup = $this->config->lookup;

        foreach ($lookup as $lookupOption) {
            $name = $lookupOption->name;
            $output .= "\n## lookup:$name\n";
            foreach ($lookupOption->list as $item) {
                $output .= "- $item\n";
            }
        }

        return $output;
    }

    public function getConfig(): RasaComponent
    {
        return $this->config;
    }


}