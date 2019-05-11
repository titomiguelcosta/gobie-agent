<?php

namespace App\Api\GroomingChimps;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;

class Client
{
    /** @var HttpClientInterface */
    private $httpClient;

    /** @var string */
    private $authToken;

    /**
     * @param HttpClientInterface $httpClient
     * @param string|null         $authToken
     */
    public function __construct(
        HttpClientInterface $httpClient,
        string $authToken = null
    ) {
        $this->httpClient = $httpClient;
        $this->authToken = $authToken;
    }

    /**
     * @param int $id
     *
     * @return array
     *
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
     * @param int   $id
     * @param array $data
     *
     * @return array
     *
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
     * @param int   $id
     * @param array $data
     *
     * @return array
     *
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

    /**
     * @param array $data
     *
     * @return array
     */
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
