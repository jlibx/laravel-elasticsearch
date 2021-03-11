<?php


namespace Golly\Elastic\Engines;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Golly\Authority\Eloquent\Model;
use Golly\Elastic\Eloquent\ElasticBuilder;
use Golly\Elastic\Eloquent\Searchable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ElasticEngine
 * @package Golly\Elastic\Engines
 */
class ElasticEngine extends Engine
{
    /**
     * @var Client
     */
    protected $elastic;

    /**
     * @var bool
     */
    protected $updateMapping = false;


    /**
     * ElasticEngine constructor.
     */
    public function __construct()
    {
        $this->elastic = ClientBuilder::create()->setHosts([
            'elasticsearch:9200'
        ])->build();
    }

    /**
     * Update the given model in the index.
     *
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
             * @var Model|Searchable $model
             */
            $params['body'][] = [
                'update' => [
                    '_id' => $model->getSearchableKey(),
                    '_index' => $model->searchableIndex(),
                ]
            ];
            $model->pushSoftDeleteMetadata();
            $params['body'][] = [
                'doc' => array_merge(
                    $model->toSearchableArray(),
                    $model->getElasticMetadata()
                ),
                'doc_as_upsert' => true
            ];
        });

        $this->elastic->bulk($params);
    }

    /**
     * Remove the given model from the index.
     *
     * @param Collection $models
     * @return void
     */
    public function delete(Collection $models)
    {
        $params['body'] = [];

        $models->each(function ($model) use (&$params) {
            $params['body'][] = [
                'delete' => [
                    '_id' => $model->getSearchableKey(),
                    '_index' => $model->searchableIndex()
                ]
            ];
        });

        $this->elastic->bulk($params);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param ElasticBuilder $builder
     * @param array $options
     * @return array
     */
    public function search(ElasticBuilder $builder, array $options = [])
    {
        $params = array_merge([
            'index' => $builder->index
        ], $builder->getBuilderQuery());

        Log::info('elastic params ' . json_encode($params));

        if ($builder->callback) {
            return call_user_func(
                $builder->callback,
                $this->elastic,
                $params
            );
        }

        return $this->elastic->search($params);
    }


    /**
     * @param ElasticBuilder $builder
     * @return mixed
     */
    public function all(ElasticBuilder $builder)
    {
        return $this->search($builder);
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param array $results
     * @return array
     */
    public function mapIds(array $results)
    {
        return collect($results['hits']['hits'])->pluck('_id')->toArray();
    }

    /**
     * @param ElasticBuilder $builder
     * @param mixed $results
     * @param Model|Searchable $model
     * @return Collection
     */
    public function map(ElasticBuilder $builder, $results, $model)
    {
        if ($this->getTotalCount($results) == 0) {
            return $model->newCollection();
        }
        $ids = $this->mapIds($results);
        $strIds = implode(',', $ids);

        return $model
            ->newQuery()
            ->orderBy(DB::raw("field(id,{$strIds})"))
            ->findMany($ids);
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param mixed $results
     * @return int
     */
    public function getTotalCount($results)
    {
        return $results['hits']['total']['value'] ?? 0;
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param Model $model
     * @return void
     */
    public function flush($model)
    {
        $model->newQuery()
            ->orderBy($model->getKeyName())
            ->unsearchable();
    }

    /**
     * Determine if the given model uses soft deletes.
     *
     * @param Model $model
     * @return bool
     */
    protected function useSoftDelete($model)
    {
        return in_array(SoftDeletes::class, class_uses_recursive($model));
    }

}
