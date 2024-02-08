# wizall-api-client

[![Latest Stable Version](https://img.shields.io/github/v/release/brokeyourbike/wizall-api-client-php)](https://github.com/brokeyourbike/wizall-api-client-php/releases)
[![Total Downloads](https://poser.pugx.org/brokeyourbike/wizall-api-client/downloads)](https://packagist.org/packages/brokeyourbike/wizall-api-client)
[![Maintainability](https://api.codeclimate.com/v1/badges/1b04658a54cfb29e4896/maintainability)](https://codeclimate.com/github/brokeyourbike/wizall-api-client-php/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/1b04658a54cfb29e4896/test_coverage)](https://codeclimate.com/github/brokeyourbike/wizall-api-client-php/test_coverage)

Wizall API client for PHP

## Installation

```bash
composer require brokeyourbike/wizall-api-client
```

## Usage

```php
use BrokeYourBike\Wizall\Client;
use BrokeYourBike\Wizall\Interfaces\ConfigInterface;

assert($config instanceof ConfigInterface);
assert($httpClient instanceof \GuzzleHttp\ClientInterface);
assert($psrCache instanceof \Psr\SimpleCache\CacheInterface);

$apiClient = new Client($config, $httpClient, $psrCache);
$apiClient->getAuthToken();
```

## Authors
- [Ivan Stasiuk](https://github.com/brokeyourbike) | [Twitter](https://twitter.com/brokeyourbike) | [LinkedIn](https://www.linkedin.com/in/brokeyourbike) | [stasi.uk](https://stasi.uk)

## License
[BSD-3-Clause License](https://github.com/brokeyourbike/wizall-api-client-php/blob/main/LICENSE)
