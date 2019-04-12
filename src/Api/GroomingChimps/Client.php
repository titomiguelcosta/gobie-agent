<?php

namespace App\Api\GroomingChimps;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;

class Client
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getJobs(): string
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET, 
            "https://api.groomingchimps.titomiguelcosta.com/index.php/jobs", 
            [
                'headers' => [
                    "accept" => "application/ld+json"
                ],
            ]
        );

        return $response->getContent();
    }
}