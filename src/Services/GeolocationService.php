<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;

class GeolocationService
{
    public function getLocationFromIp(array $ipAddress): array
    {
        $client = HttpClient::create();

        if(count($ipAddress) == 1){
            $response = $client->request('GET', 'http://ip-api.com/json/' . $ipAddress[0]);

            return $response->toArray();
        }
        return [];
    }
}