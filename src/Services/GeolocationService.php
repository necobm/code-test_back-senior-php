<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GeolocationService
{
    protected string $defaultUrl;
    protected string $defaultHttpMethod;
    protected HttpClientInterface $client;

    public function __construct()
    {
        $this->defaultUrl = "http://ip-api.com/batch";
        $this->defaultHttpMethod = 'POST';
        $this->client = HttpClient::create();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function getLocationFromIp(array $ipAddress): array
    {
        if( empty($ipAddress) ){
            throw new \Exception("You must specify at least one IP address");
        }

        $response = $this->client->request(
            $this->defaultHttpMethod,
            $this->defaultUrl,
            [
                'body' => json_encode($ipAddress)
            ]
        );

        return $this->extractLocationFromResponse($response);
    }

    private function extractLocationFromResponse(ResponseInterface $response): array
    {
        $data = $response->toArray();
        $locations = [];
        foreach ($data as $d) {
            if($d['status'] === 'success' && !empty($d['query'])){
                $locations[$d['query']] = $d['city'] . ', ' . $d['regionName'] . ', ' . $d['country'];
            }

        }

        return $locations;
    }
}