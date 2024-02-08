<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall\Tests;

use Psr\SimpleCache\CacheInterface;
use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Wizall\Responses\TokenResponse;
use BrokeYourBike\Wizall\Interfaces\ConfigInterface;
use BrokeYourBike\Wizall\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class FetchAuthTokenRawTest extends TestCase
{
    /** @test */
    public function it_can_prepare_request(): void
    {
        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://auth.example/');
        $mockedConfig->method('getClientId')->willReturn('client-id');
        $mockedConfig->method('getClientSecret')->willReturn('super-secret-value');
        $mockedConfig->method('getUsername')->willReturn('john');
        $mockedConfig->method('getPassword')->willReturn('p@ssword');
        $mockedConfig->method('getCountry')->willReturn('SN');

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "access_token": "token123",
                "expires_in": 86400,
                "token_type": "Bearer",
                "country": "SN"
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://auth.example/token/',
            [
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'grant_type' => 'password',
                    'country' => 'SN',
                    'client_id' => 'client-id',
                    'client_secret' => 'super-secret-value',
                    'client_type' => 'client_type',
                    'username' => 'john',
                    'password' => 'p@ssword',
                ],
            ],
        ])->once()->andReturn($mockedResponse);

        $mockedCache = $this->getMockBuilder(CacheInterface::class)->getMock();

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * @var CacheInterface $mockedCache
         * */
        $api = new Client($mockedConfig, $mockedClient, $mockedCache);
        $requestResult = $api->fetchAuthTokenRaw();

        $this->assertInstanceOf(TokenResponse::class, $requestResult);
        $this->assertEquals('token123', $requestResult->access_token);
        $this->assertEquals(86400, $requestResult->expires_in);
    }
}