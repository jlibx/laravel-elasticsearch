<?php

declare(strict_types=1);

namespace Golly\Elastic;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Golly\Elastic\Contracts\EngineInterface;
use Golly\Elastic\Eloquent\HasElasticsearch;
use Golly\Elastic\Hydrate\ElasticEntity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class ElasticEngine
 * @package Golly\Elastic
 */
class ElasticEngine implements EngineInterface
{

    /**
     * @var Client
     */
    protected Client $elastic;

    /**
     * @var ElasticBuilder
     */
    protected ElasticBuilder $builder;

    /**
     * ElasticEngine constructor.
     */
    public function __construct()
    {
        $this->elastic = ClientBuilder::create()->setHosts(
            config('elastic.hosts')
        )->build();
    }

    /**
     * @param array $params
     * @return $this
     */
    public function index(array $params = []): self
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
        $params = $this->builder->toSearchParams();
        // 记录执行参数
        if (app()->environment('local')) {
            Log::info('elasticsearch params ' . json_encode($params));
        }
        $result = $this->elastic->search($params);

        return ElasticEntity::instance($result, false);
    }

    /**
     * @param Collection $models
     * @return bool
     */
    public function update(Collection $models): bool
    {
        if ($models->isEmpty()) {
            return false;
        }
        $params['body'] = [];
        $models->each(function ($model) use (&$params) {
            /**
             * @var Model|HasElasticsearch $model
             */
            $params['body'][] = [
                'update' => [
                    '_id' => $model->getSearchKey(),
                    '_index' => $model->getSearchIndex(),
                ]
            ];
            $model->pushSoftDeleteMetadata();
            $params['body'][] = [
                'doc' => array_merge(
                    $model->toSearchArray(),
                    $model->getSearchMetadata()
                ),
                'doc_as_upsert' => true
            ];
        });

        $this->elastic->bulk($params);

        return true;
    }

    /**
     * @param Collection $models
     * @return bool
     */
    public function delete(Collection $models): bool
    {
        $params['body'] = [];
        $models->each(function ($model) use (&$params) {
            $params['body'][] = [
                'delete' => [
                    '_id' => $model->getSearchKey(),
                    '_index' => $model->getSearchIndex()
                ]
            ];
        });

        $this->elastic->bulk($params);

        return true;
    }

    /**
     * @param ElasticBuilder $builder
     * @return $this
     */
    public function setBuilder(ElasticBuilder $builder): static
    {
        $this->builder = $builder;

        return $this;
    }

}
