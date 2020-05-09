<?php

declare(strict_types=1);

namespace AskNicely\BPro;

use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

class Job
{
    public const FORMAT_CSV = 'csv';
    public const FORMAT_JSON = 'json';
    private const CONFIG_FILE = __DIR__ . '/../data/job.yaml';

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

    /** @var array $filters */
    private $filters;

    public function __construct(AccessTokenInterface $accessToken, Serializer $serializer)
    {
        $this->accessToken = $accessToken;
        $this->serializer = $serializer;
        $this->client = new Client();

        $conf = Yaml::parse(file_get_contents(self::CONFIG_FILE));
        $conf = $conf['job'];
        $this->url = $conf['url'];
        $this->exportCsv = $conf['export_csv'];
        $this->filters = $conf['filters'];
    }

    /**
     * @param string|null $exportFormat
     *
     * @return array
     *
     * @todo Handle JSON response pagination.
     */
    public function getJobs(?string $exportFormat = null): array
    {
        $query = ['filters' => json_encode($this->filters)];
        if ($exportFormat) {
            $this->exportCsv = $exportFormat === self::FORMAT_CSV ? 1 : 0;
        }
        if ($this->exportCsv) {
            $query['export_csv'] = 'true';
        }

        $response = $this->client->request('GET', $this->url, $this->accessToken->getToken(), $query);

        $data = $this->serializer->decode($response, $this->isOutputCsv() ? CsvEncoder::FORMAT : JsonEncoder::FORMAT);

        return $this->isOutputCsv() ? $data : $data['data']['data'];
    }

    private function isOutputCsv(): bool
    {
        return $this->outputFormat() === self::FORMAT_CSV;
    }

    private function outputFormat(): string
    {
        return $this->exportCsv ? self::FORMAT_CSV : self::FORMAT_JSON;
    }
}
