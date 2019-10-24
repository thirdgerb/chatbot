<?php


namespace Commune\Components\Rasa;


use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Components\Rasa\Providers\RasaServiceProvider;

/**
 * Rasa 组件. 可以用于调用并管理 rasa 的中间件.
 *
 * @property-read string $server  rasa http 服务端地址.
 * @property-read string $jwt  rasa http 服务的jwt.
 * @property-read int $threshold  判断匹配到的意图是否能加入 possible intent 的阈值. 是置信度 * 100
 *
 * @property-read string $output  rasa nlu 的意图配置文件地址, 通常是一个 md 文件.
 * @property-read string $domainOutput rasa domain 的文件地址, 通常是 domain.yml
 *
 * @property-read array $clientConfig http 请求的配置
 */
class RasaComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'server' => 'localhost:5005',
            'jwt' => '',
            'threshold' => 70,
            'output' => __DIR__ .'/resources/rasa.md',
            'domainOutput' => __DIR__ . '/resources/domain.md',
            'clientConfig' => [
                'timeout' => 0.5,
            ],
        ];
    }

    protected function doBootstrap(): void
    {
        $this->app->registerConversationService(RasaServiceProvider::class);
    }



}