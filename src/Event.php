<?php

declare(strict_types=1);

namespace AskNicely\BPro;

require_once __DIR__ . '/../vendor/autoload.php';

use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

class Event
{
    private const CONFIG_FILE = __DIR__ . '/../data/event.yaml';
    private const CONFIG_ID = 'event';

    /** @var AccessTokenInterface $accessToken */
    private $accessToken;

    /** @var Serializer $serializer */
    private $serializer;

    /** @var Client $client */
    private $client;

    /** @var string $url */
    private $url;

    /** @var string $exportCsv */
    private $exportCsv;

    /** @var array $params */
    private $params;

    /** @var array $columns */
    private $columns;

    public function __construct(AccessTokenInterface $accessToken, Serializer $serializer)
    {
        $this->accessToken = $accessToken;
        $this->serializer = $serializer;
        $this->client = new Client();

        $conf = Yaml::parse(file_get_contents(self::CONFIG_FILE));
        $conf = $conf[self::CONFIG_ID];
        $this->url = $conf['url'];
        $this->exportCsv = $conf['export_csv'];
        $this->params = $conf['params'];
        $this->columns = $conf['columns'];
    }

    public function getEvents(string $franchiseId, string $orderId): array
    {
        // Build filter query

        $query = [
            'franchise_id' => $franchiseId,
            'order_id' => $orderId,
        ];

        if ($this->exportCsv) {
            $query['export_csv'] = 'true';
        }

        foreach ($this->params as $param => $value) {
            if ($value === 1) {
                $query[$param] = 'true';
            }
        }

        // Fetch data

        $response = $this->serializer->decode(
            $this->client->request('GET', $this->url, $this->accessToken->getToken(), $query),
            JsonEncoder::FORMAT
        );

        // Filter and format required data

        $response = $response['data'];
        $events = [];
        foreach ($response as $event) {
            $data = ['Crew' => [], 'Truck' => []];
            foreach ($event['users'] as $user) {
                $data['Crew'][] =
                    array_intersect_key($user, array_flip(['ID', 'firstName', 'lastName', 'emailAddress']));
            }
            foreach ($event['business_assets'] as $truck) {
                $data['Truck'][] = array_intersect_key($truck, array_flip(['name_en']));
            }
            $events[] = $data;
        }

        return $events;
    }
}
