<?php

namespace SchulzeFelix\Sistrix;

use Carbon\Carbon;
use SchulzeFelix\Sistrix\Exceptions\ResponseException;

class Sistrix
{

    /**
     * @var Carbon
     */
    protected $date;

    protected $domain;
    protected $host;
    protected $path;
    protected $url;
    
    protected $requestParameterKey;
    
    protected $requestParameterValue;

    /**
     * @var SistrixClient
     */
    private $sistrixClient;


    /**
     * Sistrix constructor.
     * @param SistrixClient $sistrixClient
     */
    public function __construct(SistrixClient $sistrixClient)
    {
        $this->sistrixClient = $sistrixClient;
    }

    /**
     * @return int
     */
    public function credits()
    {
        $response = $this->performQuery('credits');
        return (int)$response[0]['credits'][0]['value'];
    }

    /**
     * @param $requestParamterValue
     * @return array
     */
    public function sichtbarkeitsindex()
    {
        $response = $this->performQuery('domain.sichtbarkeitsindex', [
            $this->requestParameterKey => $this->requestParameterValue,
            'date' => $this->getDate()
        ]);

        $array = $response[0]['sichtbarkeitsindex'][0];
        $array['date'] = Carbon::parse($array['date']);

        return $array;
    }


    public function kwCountSeo()
    {
        $response = $this->performQuery('domain.kwcount.seo', [
            $this->requestParameterKey => $this->requestParameterValue
        ]);

        $array = $response[0]['kwcount.seo'][0];
        $array['date'] = Carbon::parse($array['date']);

        return $array;
    }


    public function kwCountSeoTop10()
    {
        $response = $this->performQuery('domain.kwcount.seo.top10', [
            $this->requestParameterKey => $this->requestParameterValue,
            'date' => $this->getDate()
        ]);

        $array = $response[0]['kwcount.seo.top10'][0];
        $array['date'] = Carbon::parse($array['date']);

        return $array;
    }

    public function kwCountUs()
    {
        $response = $this->performQuery('domain.kwcount.us', [
            $this->requestParameterKey => $this->requestParameterValue,
            'date' => $this->getDate()
        ]);

        $kwcounts = collect($response[0]['kwcount.us'])
            ->map(function ($item, $key) {
                $item['date'] = Carbon::parse($item['date']);
                return $item;
            });

        return $kwcounts;
    }




    /*
     * General Query
     */

    public function performQuery($method, $parameters = [])
    {
        if(is_null($this->date)) {
            unset($parameters['date']);
        }

        $response = $this->sistrixClient->performQuery(
            $method,
            $parameters
        );


        if(isset($response['status']))
        {
            // TODO: Catch different Types of API Errors

            switch ($response['status']):
                case 'error':
                    throw new ResponseException();
                    break;
                case 'fail':
                    throw new ResponseException();
                    break;
                default:
            endswitch;
        }
        if(!isset($response['answer']))
        {
            throw new ResponseException();
        }


        return $response['answer'];
    }

    /*
     * Configuration
     */

    public function setCacheLifeTimeInMinutes(int $cacheLifeTimeInMinutes)
    {
        $this->sistrixClient->setCacheLifeTimeInMinutes($cacheLifeTimeInMinutes);

        return $this;
    }

    public function setApiKey(string $apiKey)
    {
        $this->sistrixClient->setApiKey($apiKey);

        return $this;
    }

    public function country(string $country)
    {
        $this->sistrixClient->setCountry($country);

        return $this;
    }

    public function domain($domain)
    {
        $this->requestParameterKey = 'domain';
        $this->requestParameterValue = $domain;

        return $this;
    }
    public function host($host)
    {
        $this->requestParameterKey = 'host';
        $this->requestParameterValue = $host;

        return $this;
    }
    public function path($path)
    {
        $this->requestParameterKey = 'path';
        $this->requestParameterValue = $path;

        return $this;
    }
    public function url($url)
    {
        $this->requestParameterKey = 'url';
        $this->requestParameterValue = $url;

        return $this;
    }

    public function date(Carbon $date)
    {
        $this->date = $date;

        return $this;
    }

    private function getDate()
    {
        if( is_null($this->date) )
        {
            return null;
        }

        return $this->date->toDateString();
    }


}