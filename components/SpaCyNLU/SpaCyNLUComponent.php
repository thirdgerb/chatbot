<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Components\SpaCyNLU\Configs\NLUModuleConfig;
use Commune\Components\SpaCyNLU\Configs\ChatModuleConfig;
use Commune\Components\SpaCyNLU\Impl\GuzzleSpaCyNLUClient;
use Commune\Components\SpaCyNLU\NLU\SpaCyNLUService;
use Commune\Components\SpaCyNLU\NLU\SpaCySimpleChat;
use Commune\Components\SpaCyNLU\Providers\SpaCyNLURegisterProvider;
use Commune\Components\SpaCyNLU\Providers\SpaCyServiceProvider;
use Commune\Framework\Component\AComponentOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read bool $load    启动时是否加载资源.
 *
 * # SpaCy NLU 的服务配置.
 *
 * @property-read string $host
 * @property-read string $httpClient
 * @property-read float $requestTimeOut
 *
 *
 * # 注册到 NLUManager 的服务声明.
 *
 * @property-read NLUServiceOption $nluServiceOption
 * @property-read NLUServiceOption $chatServiceOption
 *
 * # 自己的模块配置.
 *
 * @property-read NLUModuleConfig $nluModuleConfig
 * @property-read ChatModuleConfig $chatModuleConfig
 */
class SpaCyNLUComponent extends AComponentOption
{
    public static function stub(): array
    {
        return [
            'host' => '127.0.0.1:10830',
            'requestTimeOut' => 0.3,
            'load' => CommuneEnv::isLoadingResource(),

            'nluModuleConfig' => [
                'matchLimit' => 5,
                'threshold' => 0.75,
                'dataPath' => __DIR__ . '/resources/data/intents.json',
            ],
            'chatModuleConfig' => [
                'threshold' => 0.75,
                'dataPath' => __DIR__ . '/resources/data/chats.json',
            ],

            'httpClient' => GuzzleSpaCyNLUClient::class,
            'nluServiceOption' => SpaCyNLUService::defaultOption(),
            'chatServiceOption' => SpaCySimpleChat::defaultOption(),
        ];
    }

    public static function relations(): array
    {
        return [
            'nluServiceOption' => NLUServiceOption::class,
            'chatServiceOption' => NLUServiceOption::class,

            'nluModuleConfig' => NLUModuleConfig::class,
            'chatModuleConfig' => ChatModuleConfig::class,
        ];
    }

    public function bootstrap(App $app): void
    {
        // 注册 option
        $procC = $app->getProcContainer();
        $procC->instance(NLUModuleConfig::class, $this->nluModuleConfig);
        $procC->instance(ChatModuleConfig::class, $this->chatModuleConfig);

        $registrar = $app->getServiceRegistry();

        // 注册 client.
        $registrar->registerReqProvider(new SpaCyServiceProvider([
            'clientImpl' => $this->httpClient
        ]), false);

        $registrar->registerProcProvider(new SpaCyNLURegisterProvider(), false);

        if (!$this->load) {
            return;
        }

        // 加载相关管理工具.
        $this->loadPsr4MindRegister(
            $app,
            [
                "Commune\\Components\\SpaCyNLU\\Managers" => __DIR__ . '/Managers'

            ]
        );
    }


}