<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\Impl;

use Commune\Blueprint\CommuneEnv;
use GuzzleHttp\Client;
use Commune\Blueprint\Framework\Session;
use Commune\Components\SpaCyNLU\Protocals\IntentPredictionData;
use Commune\Support\Struct\InvalidStructException;
use Commune\Blueprint\Ghost\MindDef\ChatDef;
use Commune\Blueprint\Ghost\MindDef\Intent\IntentExample;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Components\SpaCyNLU\Protocals\ChatReplyData;
use Commune\Components\SpaCyNLU\Blueprint\SpaCyNLUClient;
use Commune\Components\SpaCyNLU\Protocals\NLUResponse;
use Commune\Components\SpaCyNLU\SpaCyNLUComponent;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GuzzleSpaCyNLUClient implements SpaCyNLUClient
{

    /**
     * @var SpaCyNLUComponent
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $debug;

    /**
     * GuzzleSpaCyNLUClient constructor.
     * @param SpaCyNLUComponent $config
     * @param Session $session
     */
    public function __construct(SpaCyNLUComponent $config, Session $session)
    {
        $this->config = $config;
        $this->session = $session;
        $this->debug = CommuneEnv::isDebug();
        $this->logger = $session->getLogger();
    }


    protected function getClient() : Client
    {
        return new Client(['base_uri' => $this->config->host]);
    }

    protected function request(
        string $method,
        string $uri,
        array $option
    ) : NLUResponse
    {
        $option['timeout'] = $this->config->requestTimeOut;
        if ($this->debug) {
            $start = microtime(true);
            // 记录 debug 日志.
            $this->logger->debug(
                __METHOD__
                . ' start : '
                . json_encode([
                    'method' => $method,
                    'uri' => $uri,
                    'option' => $option
                ], JSON_UNESCAPED_UNICODE)
            );
        }

        $client = $this->getClient();
        try {
            $res = $client->request(
                $method,
                $uri,
                $option
            );

            $content = $res->getBody()->getContents();

            if ($this->debug) {
                $end = microtime(true);
                $gap = ceil(($end - $start) * 1000000);
                $this->logger->debug(
                    __METHOD__
                    . " response in $gap us : $content"
                );
            }


            $resProto = json_decode($content, true);
            if (!is_array($resProto)) {
                $error = json_last_error();
                $this->logger->warning(
                    __METHOD__. " invalid response protocal: $error"
                );
            }

            $response = new NLUResponse($resProto);
            if (! $response->isSuccess()) {
                $this->logger->warning(
                    __METHOD__
                    . ' is failed, '
                    . ' code is : '
                    . $response->getCode()
                    . ' message is : '
                    . $response->getMessage()
                );
            }
            return $response;

        } catch (InvalidStructException $e) {

            $this->logger->error($e);
            return new NLUResponse([
                'code' => 500,
                'msg' => 'invalid protocal: ' . $e->getMessage()
            ]);

        } catch (GuzzleException $e) {
            $this->logger->warning($e);
            return new NLUResponse([
                'code' => 500,
                'msg' => 'request failed: '. $e->getMessage()
            ]);
        }

    }

    public function intentLearn(IntentDef $def): ? string
    {
        $data = [
            'label' => $def->getIntentName(),
            'examples' => array_map(function(IntentExample $example) {
                return $example->getText();
            }, $def->getExampleObjects())
        ];
        $body = json_encode($data);

        $response = $this->request(
            'POST',
            'classifier/learn-intent',
            [
                'body' => $body
            ]
        );

        return $response->isSuccess() ? null : $response->getMessage();
    }


    public function intentPredict(
        string $sentence,
        array $possibles,
        float $threshold,
        int $limit = 5
    ): array
    {
        $data = [
            'sentence' => $sentence,
            'possibles' => $possibles,
            'threshold' => (float) $threshold,
            'limit' => $limit
        ];
        $body = json_encode($data);
        $response = $this->request(
            'GET',
            '/classifier/predict',
            ['body' => $body]
        );

        $proto = $response->getProtocalData();
        if (empty($proto)) {
            return [];
        }

        $result = [];

        try {
            foreach ($proto as $protoData) {
                $result[] = new IntentPredictionData($protoData);
            }

            return $result;

        } catch (\Throwable $e) {
            $this->logger->error($e);
            return $result;
        }
    }

    public function chatLearn(ChatDef $def) : ? string
    {
        $data = [
            'cid' => $def->getCid(),
            'say' => $def->getSay(),
            'reply' => $def->getReply(),
            'index' => $def->getIndex(),
        ];
        $body = json_encode($data);
        $response = $this->request(
            'POST',
            '/chat/learn',
            ['body' => $body]
        );

        return $response->isSuccess() ? null : $response->getMessage();
    }


    public function chatReply(
        string $say,
        float $threshold,
        string $index = ''
    ): ? ChatReplyData
    {
        $data = [
            'say' => $say,
            'threshold' => (float) $threshold,
            'index' => $index
        ];
        $body = json_encode($data);

        $response = $this->request(
            'GET',
            '/chat/reply',
            ['body' => $body]
        );

        try {
            $reply = new ChatReplyData($response->getProtocalData());
            if ($reply->isEmpty()) {
                return null;
            }
            return $reply;

        } catch (\Throwable $e) {
            $this->logger->error($e);
            return null;
        }
    }


}