<?php

declare(strict_types=1);

namespace Golly\Elastic;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Golly\Elastic\Contracts\EngineInterface;
use Golly\Elastic\Hydrate\ElasticEntity;

/**
 * Class ElasticEngine
 * @package Golly\Elastic
 */
class Engine implements EngineInterface
{

    protected array $hosts = [];

    /**
     * @var Client
     */
    protected Client $elastic;

    /**
     * @var Builder
     */
    protected Builder $builder;

    /**
     * ElasticEngine constructor.
     */
    public function __construct(array $hosts)
    {
        $this->elastic = ClientBuilder::create()->setHosts($hosts)->build();
    }

    /**
     * @param array $params
     * @return $this
     */
    public function index(array $params = []): static
    {
        $this->elastic->index($params);

        return $this;
    }

    /**
     * @param array $options
     * @return ElasticEntity
     */
    public function search(array $options = []): ElasticEntity
    {
        $result = $this->elastic->search(
            $this->builder->toSearchParams()
        );

        return ElasticEntity::instance($result, false);
    }

    /**
     * @param array $params
     * @return array
     */
    public function bulk(array $params): array
    {
        return $this->elastic->bulk($params);
    }

    /**
     * @param Builder $builder
     * @return $this
     */
    public function setBuilder(Builder $builder): static
    {
        $this->builder = $builder;

        return $this;
    }

}
