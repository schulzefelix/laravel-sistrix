<?php namespace Fschulze\Sistrix;

use GuzzleHttp\Client;
use Illuminate\Contracts\Cache\Repository;

class SistrixClient
{
    /*
     * Method
     * Key
     * Country
     * Data
     */
    CONST SISTRIX_API_ENDPOINT = '%s?api_key=%s&country=%s&format=json%s';

    /**
     * @var Client
     */
    protected $client;
    /**
     * @var int
     */
    protected $cacheLifeTimeInMinutes = 0;

    /**
     * @var string
     */
    protected $apiCountry;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var Repository
     */
    private $cache;

    /**
     * Sistrix constructor.
     * @param Client $client
     * @param Repository $cache
     */
    public function __construct(Client $client, Repository $cache)
    {
        $this->client = $client;

        $this->cache = $cache;
    }

    /**
     * Set the cache time.
     *
     * @param int $cacheLifeTimeInMinutes
     *
     * @return self
     */
    public function setCacheLifeTimeInMinutes(int $cacheLifeTimeInMinutes)
    {
        $this->cacheLifeTimeInMinutes = $cacheLifeTimeInMinutes;
        return $this;
    }

    /**
     * Set the API key.
     *
     * @param string $apiKey
     * @return static
     *
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Set the API country Reference.
     *
     * @param string $apiCountry
     * @return static
     */
    public function setCountry(string $apiCountry)
    {
        $this->apiCountry = $apiCountry;
        return $this;
    }


    public function performQuery(string $method, array $parameters)
    {
        $cacheName = $this->determineCacheName(func_get_args());
        if ($this->cacheLifeTimeInMinutes == 0) {
            $this->cache->forget($cacheName);
        }
        return $this->cache->remember($cacheName, $this->cacheLifeTimeInMinutes, function () use ($method, $parameters) {
            $request = $this->buildRequest($method, $parameters);
            $response = $this->client->get($request);
            return json_decode($response->getBody()->getContents(), true);
        });
    }

    protected function buildRequest($method, $parameters = [])
    {
        $parameterString = '';

        foreach ($parameters as $parameter => $value) {
            $parameterString .= '&' . $parameter . '=' . $value;
        }

        $request = sprintf(self::SISTRIX_API_ENDPOINT, $method, $this->apiKey, $this->apiCountry, $parameterString);

        return $request;
    }


    protected function determineCacheName(array $properties): string
    {
        return 'fschulze.laravel-sistrix.'.md5(serialize($properties));
    }


}