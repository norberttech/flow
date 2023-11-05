<?php

declare(strict_types=1);

namespace Flow\ETL\Adapter\Elasticsearch\Tests\Context;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Flow\ETL\Adapter\Elasticsearch\IdFactory;
use Flow\ETL\Config;
use Flow\ETL\DSL\Elasticsearch;
use Flow\ETL\FlowContext;
use Flow\ETL\Rows;

final class Elasticsearch7Context implements ElasticsearchContext
{
    private ?Client $client = null;

    public function __construct(private readonly array $hosts)
    {
    }

    public function client() : Client
    {
        if ($this->client === null) {
            $this->client = ClientBuilder::fromConfig($this->clientConfig());
        }

        return $this->client;
    }

    public function clientConfig() : array
    {
        return [
            'hosts' => $this->hosts,
        ];
    }

    public function createIndex(string $name) : void
    {
        try {
            $params = [
                'index' => $name,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 2,
                        'number_of_replicas' => 0,
                    ],
                ],
            ];

            $response = $this->client()->indices()->create($params);
        } catch (BadRequest400Exception) {
        }
    }

    public function deleteIndex(string $name) : void
    {
        try {
            $deleteParams = [
                'index' => $name,
            ];
            $response = $this->client()->indices()->delete($deleteParams);
        } catch (Missing404Exception) {
        }
    }

    public function loadRows(Rows $rows, string $index, IdFactory $idFactory) : void
    {
        Elasticsearch::bulk_index(
            $this->clientConfig(),
            $index,
            $idFactory,
            ['refresh' => true]
        )
            ->load($rows, new FlowContext(Config::default()));
    }
}
