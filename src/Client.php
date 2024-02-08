<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall;

use Psr\SimpleCache\CacheInterface;
use GuzzleHttp\ClientInterface;
use BrokeYourBike\Wizall\Responses\TransferCashStatusResponse;
use BrokeYourBike\Wizall\Responses\TransferCashResponse;
use BrokeYourBike\Wizall\Responses\TokenResponse;
use BrokeYourBike\Wizall\Interfaces\TransactionInterface;
use BrokeYourBike\Wizall\Interfaces\ConfigInterface;
use BrokeYourBike\ResolveUri\ResolveUriTrait;
use BrokeYourBike\HttpEnums\HttpMethodEnum;
use BrokeYourBike\HttpClient\HttpClientTrait;
use BrokeYourBike\HttpClient\HttpClientInterface;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\HasSourceModel\HasSourceModelTrait;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class Client implements HttpClientInterface
{
    use HttpClientTrait;
    use ResolveUriTrait;
    use HasSourceModelTrait;

    private ConfigInterface $config;
    private CacheInterface $cache;

    public function __construct(ConfigInterface $config, ClientInterface $httpClient, CacheInterface $cache)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    public function authTokenCacheKey(): string
    {
        return get_class($this) . ':authToken:';
    }

    public function getAuthToken(): string
    {
        if ($this->cache->has($this->authTokenCacheKey())) {
            $cachedToken = $this->cache->get($this->authTokenCacheKey());
            if (is_string($cachedToken)) {
                return $cachedToken;
            }
        }

        $response = $this->fetchAuthTokenRaw();

        $this->cache->set(
            $this->authTokenCacheKey(),
            $response->access_token,
            (int) $response->expires_in / 2
        );

        return $response->access_token;
    }


    public function fetchAuthTokenRaw(): TokenResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
            \GuzzleHttp\RequestOptions::FORM_PARAMS => [
                'grant_type' => 'password',
                'country' => $this->config->getCountry(),
                'client_id' => $this->config->getClientId(),
                'client_secret' => $this->config->getClientSecret(),
                'username' => $this->config->getUsername(),
                'password' => $this->config->getPassword(),
            ],
        ];

        $response = $this->httpClient->request(
            HttpMethodEnum::POST->value,
            (string) $this->resolveUriFor($this->config->getUrl(), "token/"),
            $options
        );

        return new TokenResponse($response);
    }

    public function transferCash(TransactionInterface $transaction): TransferCashResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->getAuthToken()}",
            ],
            \GuzzleHttp\RequestOptions::JSON => [
                'agent_msisdn' => $this->config->getAgentMsisdn(),
                'agent_pin' => $this->config->getAgentPin(),
            ],
        ];

        if ($transaction instanceof SourceModelInterface){
            $options[\BrokeYourBike\HasSourceModel\Enums\RequestOptions::SOURCE_MODEL] = $transaction;
        }

        $response = $this->httpClient->request(
            HttpMethodEnum::POST->value,
            (string) $this->resolveUriFor($this->config->getUrl(), "api/transfert/cash/"),
            $options
        );
        
        return new TransferCashResponse($response);
    }

    public function cashStatus(string $transactionId, string $country): TransferCashStatusResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->getAuthToken()}",
            ],
            \GuzzleHttp\RequestOptions::JSON => [
                'agent_msisdn' => $this->config->getAgentMsisdn(),
                'agent_pin' => $this->config->getAgentPin(),
                'transaction_id' => $transactionId,
                'country' => $country,
            ],
        ];

        $response = $this->httpClient->request(
            HttpMethodEnum::POST->value,
            (string) $this->resolveUriFor($this->config->getUrl(), "api/statut/bon/cash/"),
            $options
        );

        return new TransferCashStatusResponse($response);
    }
}