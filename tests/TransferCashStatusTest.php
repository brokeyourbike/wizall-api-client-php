<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall\Tests;

use Psr\SimpleCache\CacheInterface;
use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Wizall\Responses\TransferCashStatusResponse;
use BrokeYourBike\Wizall\Responses\TransferCashResponse;
use BrokeYourBike\Wizall\Interfaces\TransactionInterface;
use BrokeYourBike\Wizall\Interfaces\ConfigInterface;
use BrokeYourBike\Wizall\Enums\TransactionStatusEnum;
use BrokeYourBike\Wizall\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class TransferCashStatusTest extends TestCase
{
    /** @test */
    public function it_can_prepare_request(): void
    {
        $transaction = $this->getMockBuilder(TransactionInterface::class)->getMock();

        /** @var TransactionInterface $transaction */
        $this->assertInstanceOf(TransactionInterface::class, $transaction);

        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $mockedConfig->method('getAgentMsisdn')->willReturn('123456789');
        $mockedConfig->method('getAgentPin')->willReturn('0088');

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "Operation": "Check_status_bon_cash",
                "transactionid": "274126155",
                "SenderPhone": "0182491234",
                "Amount": "100.000000",
                "ReedemWalletType": "",
                "ReedemTransactionID": "",
                "ReedemPhone": "",
                "ReceiverPhone": "0182491234",
                "TokenStatus": "Initiated",
                "ReedemTime": "",
                "code": "200"
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->once()->andReturn($mockedResponse);

        $mockedCache = $this->getMockBuilder(CacheInterface::class)->getMock();
        $mockedCache->method('has')->willReturn(true);
        $mockedCache->method('get')->willReturn('secure-token');

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * @var CacheInterface $mockedCache
         * */
        $api = new Client($mockedConfig, $mockedClient, $mockedCache);

        $requestResult = $api->transferCashStatus('274126155');
        $this->assertInstanceOf(TransferCashStatusResponse::class, $requestResult);
        $this->assertEquals(TransactionStatusEnum::INITIATED->value, $requestResult->status);
    }

    /** @test */
    public function it_can_handle_failure(): void
    {
        $transaction = $this->getMockBuilder(TransactionInterface::class)->getMock();

        /** @var TransactionInterface $transaction */
        $this->assertInstanceOf(TransactionInterface::class, $transaction);

        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $mockedConfig->method('getAgentMsisdn')->willReturn('123456789');
        $mockedConfig->method('getAgentPin')->willReturn('0088');

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(400);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "code": 400,
                "error": "transaction_id not valide"
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->once()->andReturn($mockedResponse);

        $mockedCache = $this->getMockBuilder(CacheInterface::class)->getMock();
        $mockedCache->method('has')->willReturn(true);
        $mockedCache->method('get')->willReturn('secure-token');

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * @var CacheInterface $mockedCache
         * */
        $api = new Client($mockedConfig, $mockedClient, $mockedCache);

        $requestResult = $api->transferCashStatus('274126155');
        $this->assertInstanceOf(TransferCashStatusResponse::class, $requestResult);
        $this->assertEquals('transaction_id not valide', $requestResult->error);
    }
}