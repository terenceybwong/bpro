<?php

declare(strict_types=1);

namespace AskNicely\BPro;

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client as HttpClient;

class Client
{
    /** @var HttpClient $client */
    private $client;

    public function __construct()
    {
        $this->client = new HttpClient();
    }

    public function request(
        string $method,
        string $url,
        string $accessToken,
        ?array $query = null,
        ?array $body = null
    ): string {
        $config = [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json, text/csv, text/plain',
                'Connection' => 'keep-alive',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'no-cache',
            ],
        ];

        if ($query) {
            $config['query'] = $query;
        }
        if ($body) {
            $config['body'] = $body;
        }

        $response = $this->client->request($method, $url, $config);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Request unsuccessful');
        }

        return $response->getBody()->getContents();
    }
}
