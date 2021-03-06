<?php

namespace App\Api\GroomingChimps;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    /** @var HttpClientInterface */
    private $httpClient;

    /** @var string */
    private $authToken;

    public function __construct(
        HttpClientInterface $httpClient,
        ?string $authToken = null
    ) {
        $this->httpClient = $httpClient;
        $this->authToken = $authToken;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getJob(int $id): array
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            '/jobs/'.$id,
            $this->getOptions([])
        );

        return $response->toArray();
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function putJob(int $id, array $data): array
    {
        $response = $this->httpClient->request(
            Request::METHOD_PUT,
            '/jobs/'.$id,
            $this->getOptions($data)
        );

        return $response->toArray();
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function putTask(int $id, array $data): array
    {
        $response = $this->httpClient->request(
            Request::METHOD_PUT,
            '/tasks/'.$id,
            $this->getOptions($data)
        );

        return $response->toArray();
    }

    private function getOptions(array $data): array
    {
        $options = [];
        $options['json'] = $data;
        $options['headers'] = [
            'accept' => 'application/ld+json',
            'content-type' => 'application/ld+json',
        ];

        if ($this->authToken) {
            $options['auth_bearer'] = $this->authToken;
        }

        return $options;
    }
}
