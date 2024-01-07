<?php

namespace App\Api\Gobie;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private ?string $authToken = null
    ) {
        $this->httpClient = $httpClient;
        $this->authToken = $authToken;
    }

    public function getJob(int $id): array
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            sprintf('/jobs/%d', $id),
            $this->getOptions([])
        );

        return $response->toArray();
    }

    public function putJob(int $id, array $data): array
    {
        $response = $this->httpClient->request(
            Request::METHOD_PUT,
            sprintf('/jobs/%d', $id),
            $this->getOptions($data)
        );

        return $response->toArray();
    }

    public function putTask(int $id, array $data): array
    {
        $response = $this->httpClient->request(
            Request::METHOD_PUT,
            sprintf('/tasks/%d', $id),
            $this->getOptions($data)
        );

        return $response->toArray();
    }

    private function getOptions(array $data): array
    {
        $options = [
            'json' => $data,
            'headers' => [
                'accept' => 'application/ld+json',
                'content-type' => 'application/ld+json',
            ],
        ];

        if ($this->authToken) {
            $options['auth_bearer'] = $this->authToken;
        }

        return $options;
    }
}
