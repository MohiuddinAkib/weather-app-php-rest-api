<?php

namespace App\models;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Weather
{
    private $url = 'https://www.metaweather.com/api/location';
    private $client;
    private $weatherOfSixCities = [];
    private $woeids = [2344116, 638242, 44418, 565346, 560743, 9807];
    private $weatherOfSearchedCities = [];
    private $weatherDetails;
    
    public function __construct()
    {
        // Init client
        $this->client = new Client();
        
        // Set url based on requested route
        if (isset($_GET['command'])) {
            $command = $_GET['command'];
            if ($command === 'search') {
                $this->url = $this->url . "/search/?query={$_GET['keyword']}";
            } elseif ($command === 'location') {
                $this->url = $this->url . "/{$_GET['woeid']}";
            }
        }
    }
    
    /**
    * Fetches weather of Istanbul, Berlin, London, Helsinki, Dublin, Vancouver
    * ROUTE:  http://localhost:8000/weather.php
    *
    * @return response
    * @method GET
    */
    public function getWeatherOfSixCities()
    {
        try {
            foreach ($this->woeids as $woeid) {
                $request = new Request('GET', "{$this->url}/{$woeid}");
                $promise = $this->client->sendAsync($request)->then(function ($response) {
                    if ($response->getStatusCode() === 200) {
                        $body = $response->getBody();
                        $decoded_body = json_decode($body, true);
                        extract($decoded_body["consolidated_weather"][0]);
                        $result = array(
                            'city_name' => $decoded_body['title'],
                            'woeid' => $decoded_body['woeid'],
                            'the_temp' => $the_temp,
                            'min_temp' => $min_temp,
                            'max_temp' => $max_temp,
                            'icon' => "https://www.metaweather.com/static/img/weather/{$weather_state_abbr}.svg",
                        );
                        array_push($this->weatherOfSixCities, $result);
                    }
                });
                $promise->wait();
            }
            return $this->weatherOfSixCities;
        } catch (\Exception $e) {
            die("The following exceptions were encountered:\n  {$e->getMessage()} . \n");
        }
    }
    
    /**
    * Fetch weather by city name
    * ROUTE:  http://localhost:8000/weather.php?command=search&keyword=dhaka
    *
    * @return response
    * @method GET
    */
    public function searchLocation()
    {
        try {
            $s_request = new Request('GET', $this->url);
            $promise = $this->client->sendAsync($s_request)->then(function ($response) {
                $body = $response->getBody();
                $decoded_body = json_decode($body, true);
                
                foreach ($decoded_body as $city) {
                    $l_request = new Request('GET', "https://www.metaweather.com/api/location/{$city['woeid']}");
                    $promise = $this->client->sendAsync($l_request)->then(function ($response) {
                        $body = $response->getBody();
                        $decoded_body = json_decode($body, true);
                        extract($decoded_body["consolidated_weather"][0]);
                        $result = array(
                            'city_name' => $decoded_body['title'],
                            'the_temp' => $the_temp,
                            'min_temp' => $min_temp,
                            'max_temp' => $max_temp,
                            'icon' => "https://www.metaweather.com/static/img/weather/{$weather_state_abbr}.svg",
                        );
                        array_push($this->weatherOfSearchedCities, $result);
                    });
                    $promise->wait();
                }
            });
            $promise->wait();
            return $this->weatherOfSearchedCities;
        } catch (\Exception $e) {
            die("The following exceptions were encountered:\n  {$e->getMessage()} . \n");
        }
    }


    /**
     * Show details of selected city
     * ROUTE:  http://localhost:8000/weather.php?woeid=44418&command=location
     *
     * @return response
     * @method GET
     */
    public function searchWeathers()
    {
        try {
            $request = new Request('GET', $this->url);
            $promise = $this->client->sendAsync($request)->then(function ($response) {
                $body = $response->getBody();
                $decoded_body = json_decode($body, true);
                $weathers = array_map(function ($weather) {
                    extract($weather);
                    return array(
                        'id' => $id,
                        'applicable_day' => (new DateTime($applicable_date))->format('l'),
                        'the_temp' => $the_temp,
                        'min_temp' => $min_temp,
                        'max_temp' => $max_temp,
                        'icon' => "https://www.metaweather.com/static/img/weather/{$weather_state_abbr}.svg",
                    );
                }, $decoded_body["consolidated_weather"]) ;
                $result = array(
                    'weathers' => $weathers,
                    'city_name' => $decoded_body['title']
                );
                $this->weatherDetails = $result;
            });
            $promise->wait();
            return $this->weatherDetails;
        } catch (\Exception $e) {
            die("The following exceptions were encountered:\n  {$e->getMessage()} . \n");
        }
    }
}
