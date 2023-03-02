<?php

namespace Services;

use App\Services\GeolocationService;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GeolocationServiceTest extends \PHPUnit\Framework\TestCase
{
    private GeolocationService $geolocationService;
    private HttpClientInterface $httpClient;
    private ResponseInterface $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->geolocationService = new GeolocationService();
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testGetLocationFromIp()
    {
        $responseArray = [
            [
                "status" => "success",
                "country" => "United Kingdom",
                "countryCode" => "GB",
                "region" => "ENG",
                "regionName" => "England",
                "city" => "Billingham",
                "zip" => "TS23",
                "lat" => 54.5896,
                "lon" => -1.2891,
                "timezone" => "Europe/London",
                "isp" => "Sky UK Limited",
                "org" => "Sky Broadband",
                "as" => "AS5607 Sky UK Limited",
                "query" => "188.223.227.125"
            ]
        ];
        $this->response->method('toArray')->willReturn($responseArray);
        $this->httpClient->method('request')->willReturn($this->response);
        $this->geolocationService->setClient($this->httpClient);

        $res = $this->geolocationService->getLocationFromIp(["188.223.227.125"]);
        $this->assertIsArray($res);
        $this->assertContains('Billingham, England, United Kingdom', $res);
        $this->assertArrayHasKey('188.223.227.125', $res);
    }

    public function testGetLocationFromIpWithEmptyIpAddressList()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("You must specify at least one IP address");
        $res = $this->geolocationService->getLocationFromIp([]);
    }

    public function testGetLocationFromIpWithSomeInvalidIpAddress()
    {
        $ipsToLookOut = ["188.223.227.125","","233","95.61.1.46"];
        $responseArray = [
            [
                "status" => "success",
                "country" => "United Kingdom",
                "countryCode" => "GB",
                "region" => "ENG",
                "regionName" => "England",
                "city" => "Billingham",
                "zip" => "TS23",
                "lat" => 54.5896,
                "lon" => -1.2891,
                "timezone" => "Europe/London",
                "isp" => "Sky UK Limited",
                "org" => "Sky Broadband",
                "as" => "AS5607 Sky UK Limited",
                "query" => "188.223.227.125"
            ],
            [
                "status" => "fail",
                "message" => "invalid query",
                "query" => ""
            ],
            [
                "status" => "fail",
                "message" => "invalid query",
                "query" => "233"
            ],
            [
                "status" => "success",
                "country" => "Spain",
                "countryCode" => "ES",
                "region" => "AN",
                "regionName" => "Andalusia",
                "city" => "Bollullos de la Mitacion",
                "zip" => "41110",
                "lat" => 37.3391,
                "lon" => -6.1411,
                "timezone" => "Europe/Madrid",
                "isp" => "Vodafone Espana S.A.U.",
                "org" => "Comunitel Global S.A.",
                "as" => "AS12430 VODAFONE ESPANA S.A.U.",
                "query" => "95.61.1.46"
            ]
        ];
        $this->response->method('toArray')->willReturn($responseArray);
        $this->httpClient->method('request')->willReturn($this->response);
        $this->geolocationService->setClient($this->httpClient);

        $res = $this->geolocationService->getLocationFromIp($ipsToLookOut);
        $this->assertIsArray($res);
        $this->assertContains('Billingham, England, United Kingdom', $res);
        $this->assertContains('Bollullos de la Mitacion, Andalusia, Spain', $res);
        $this->assertArrayHasKey('188.223.227.125', $res);
        $this->assertArrayHasKey('95.61.1.46', $res);
        $this->assertArrayNotHasKey('233', $res);
        $this->assertCount(2, $res);
    }

}