<?php

namespace Yansongda\Pay\Tests\Plugin\Wechat\V3\Marketing\MchTransfer;

use Yansongda\Artful\Exception\InvalidParamsException;
use Yansongda\Artful\Rocket;
use Yansongda\Pay\Exception\Exception;
use Yansongda\Pay\Plugin\Wechat\V3\Marketing\MchTransfer\QueryPlugin;
use Yansongda\Pay\Tests\TestCase;
use Yansongda\Supports\Collection;

class QueryPluginTest extends TestCase
{
    protected QueryPlugin $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plugin = new QueryPlugin();
    }

    public function testModeWrong()
    {
        $rocket = new Rocket();
        $rocket->setParams(['_config' => 'service_provider']);

        self::expectException(InvalidParamsException::class);
        self::expectExceptionCode(Exception::PARAMS_PLUGIN_ONLY_SUPPORT_NORMAL_MODE);
        self::expectExceptionMessage('参数异常: 通过商户单号查询转账单，只支持普通商户模式，当前配置为服务商模式');

        $this->plugin->assembly($rocket, function ($rocket) { return $rocket; });
    }

    public function testEmptyPayload()
    {
        $rocket = new Rocket();

        self::expectException(InvalidParamsException::class);
        self::expectExceptionCode(Exception::PARAMS_NECESSARY_PARAMS_MISSING);
        self::expectExceptionMessage('参数异常: 通过商户单号查询转账单，参数缺少 `out_bill_no`');

        $this->plugin->assembly($rocket, function ($rocket) { return $rocket; });
    }

    public function testNormalParams()
    {
        $rocket = new Rocket();
        $rocket->setPayload(new Collection( [
            "out_bill_no" => "111",
        ]));

        $result = $this->plugin->assembly($rocket, function ($rocket) { return $rocket; });

        self::assertEquals([
            '_method' => 'GET',
            '_url' => 'v3/fund-app/mch-transfer/transfer-bills/out-bill-no/111',
        ], $result->getPayload()->all());
    }

    public function testTransferBillNoParams()
    {
        $rocket = new Rocket();
        $rocket->setPayload(new Collection( [
            "transfer_bill_no" => "111",
        ]));

        $result = $this->plugin->assembly($rocket, function ($rocket) { return $rocket; });

        self::assertEquals([
            '_method' => 'GET',
            '_url' => 'v3/fund-app/mch-transfer/transfer-bills/transfer-bill-no/111',
        ], $result->getPayload()->all());
    }
}
