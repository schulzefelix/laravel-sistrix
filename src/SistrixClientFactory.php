<?php

namespace SchulzeFelix\Sistrix;

use GuzzleHttp\Client;
use Illuminate\Contracts\Cache\Repository;

class SistrixClientFactory
{

    public static function createForConfig(array $sistrixConfig): SistrixClient
    {
        $guzzleClient = new Client(
            [
                'headers' => [
                    'User-Agent' => 'Laravel Sistrix Client',
                    'Accept-Encoding' => 'gzip, deflate, sdch'
                ],
                // Base URI is used with relative requests
                'base_uri' => 'https://api.sistrix.com/',
                'timeout'  => 60.0,
            ]
        );

        return self::createSistrixClient($sistrixConfig, $guzzleClient);
    }

    protected static function createSistrixClient(array $sistrixConfig, Client $guzzleClient): SistrixClient
    {
        $client = new SistrixClient($guzzleClient, app(Repository::class));
        $client->setApiKey($sistrixConfig['key']);
        $client->setCountry($sistrixConfig['default_country']);
        $client->setCacheLifeTimeInMinutes($sistrixConfig['cache_lifetime_in_minutes']);
        return $client;
    }

}