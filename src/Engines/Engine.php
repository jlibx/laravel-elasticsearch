<?php


namespace Golly\Elastic\Engines;


use Golly\Elastic\Eloquent\ElasticBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Engine
 * @package Golly\Elastic\Engines
 */
abstract class Engine
{
    /**
     * Update the given model in the index.
     *
     * @param Collection $models
     * @return void
     */
    abstract public function update(Collection $models);

    /**
     * Remove the given model from the index.
     *
     * @param Collection $models
     * @return void
     */
    abstract public function delete(Collection $models);

    /**
     * Perform the given search on the engine.
     *
     * @param ElasticBuilder $builder
     * @param array $options
     * @return mixed
     */
    abstract public function search(ElasticBuilder $builder, array $options = []);

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param array $results
     * @return array
     */
    abstract public function mapIds(array $results);

    /**
     * Map the given results to instances of the given model.
     *
     * @param ElasticBuilder $builder
     * @param array $results
     * @param $model
     * @return mixed
     */
    abstract public function map(ElasticBuilder $builder, array $results, $model);

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param array $results
     * @return int
     */
    abstract public function getTotalCount(array $results);

    /**
     * Flush all of the model's records from the engine.
     *
     * @param Model $model
     * @return void
     */
    abstract public function flush($model);

    /**
     *  Get the results of the query as a Collection of primary keys.
     *
     * @param ElasticBuilder $builder
     * @return array
     */
    public function keys(ElasticBuilder $builder)
    {
        return $this->mapIds($this->search($builder));
    }

    /**
     * Get the results of the given query mapped onto models.
     *
     * @param ElasticBuilder $builder
     * @return Collection
     */
    public function get(ElasticBuilder $builder)
    {
        return $this->map(
            $builder,
            $this->search($builder),
            $builder->model
        );
    }
}
