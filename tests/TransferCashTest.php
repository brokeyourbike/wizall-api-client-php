<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall\Tests;

use Psr\SimpleCache\CacheInterface;
use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Wizall\Responses\TransferCashResponse;
use BrokeYourBike\Wizall\Interfaces\TransactionInterface;
use BrokeYourBike\Wizall\Interfaces\ConfigInterface;
use BrokeYourBike\Wizall\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class TransferCashTest extends TestCase
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
                "Operation": "PickUpCode",
                "id": 124512345,
                "code": "200",
                "status": "valid",
                "value": "100.0",
                "currency_code": 952,
                "paid_value": 100,
                "paid_value_currency_code": 952,
                "is_cash": 0,
                "fee": "0",
                "commission": "0.0",
                "timestamp": "1889-02-08T10:51:42",
                "solde": 12434,
                "details": {
                    "message": "PickUpCode successful",
                    "service": "customer-sell-good-voucher",
                    "rid": "1lj2h34lj1234h"
                }
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

        $requestResult = $api->transferCash($transaction);
        $this->assertInstanceOf(TransferCashResponse::class, $requestResult);
        $this->assertEquals('124512345', $requestResult->id);
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
                "code": "WZ0001",
                "error": "this transaction already exist",
                "external_trx_id": "GL00003",
                "trans_id": "124512345",
                "details": {
                    "message": null,
                    "service": "customer-sell-good-voucher",
                    "rid": "1kl24jl1k24j1"
                }
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

        $requestResult = $api->transferCash($transaction);
        $this->assertInstanceOf(TransferCashResponse::class, $requestResult);
        $this->assertEquals('this transaction already exist', $requestResult->error);
    }
}