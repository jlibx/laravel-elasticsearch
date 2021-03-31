<?php


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
    protected $elastic;

    /**
     * @var ElasticBuilder
     */
    protected $builder;

    /**
     * ElasticEngine constructor.
     */
    public function __construct()
    {
        $this->elastic = ClientBuilder::create()->setHosts(
            config('elastic.hosts')
        )->build();
    }

    public function index()
    {
        $this->elastic->index();
    }

    /**
     * @param array $options
     * @return ElasticEntity
     */
    public function search(array $options = [])
    {
        $params = $this->builder->toSearchParams();
        // 记录执行参数
        Log::info('elasticsearch params ' . json_encode($params));
        $result = $this->elastic->search($params);

        return ElasticEntity::instance($result, false);
    }

    /**
     * @param Collection $models
     * @return void
     */
    public function update(Collection $models)
    {
        if ($models->isEmpty()) {
            return;
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
            // TODO 软删删除
            $params['body'][] = [
                'doc' => array_merge(
                    $model->toSearchSource(),
                    $model->getSearchMetadata()
                ),
                'doc_as_upsert' => true
            ];
        });

        $this->elastic->bulk($params);
    }

    /**
     * @param Collection $models
     * @return void
     */
    public function delete(Collection $models)
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
    }

    /**
     * @param ElasticBuilder $builder
     */
    public function setBuilder(ElasticBuilder $builder)
    {
        $this->builder = $builder;
    }

}
