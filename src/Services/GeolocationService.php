<?php

/**
 * Service to handle ip-api request to get data related with IP Address. See https://ip-api.com/ for more details
 */

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
     * Request ip-api endpoint to get locations from a given list of IP addresses
     *
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception|DecodingExceptionInterface
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

    /**
     * Extract only locations related parameters from the response and return they as array format
     *
     * @param ResponseInterface $response
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
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

    /**
     * @return HttpClientInterface
     */
    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }

    /**
     * @param HttpClientInterface $client
     */
    public function setClient(HttpClientInterface $client): void
    {
        $this->client = $client;
    }


}