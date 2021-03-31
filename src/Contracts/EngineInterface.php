<?php


namespace Golly\Elastic\Contracts;


use Golly\Elastic\Eloquent\Builder;
use Golly\Elastic\Entities\ElasticEntity;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface EngineInterface
 * @package Golly\Elastic\Contracts
 */
interface EngineInterface
{

    /**
     * Update the given model in the index.
     *
     * @param Collection $models
     * @return void
     */
    public function update(Collection $models);

    /**
     * Remove the given model from the index.
     *
     * @param Collection $models
     * @return void
     */
    public function delete(Collection $models);

    /**
     * Perform the given search on the engine.
     *
     * @param array $options
     * @return ElasticEntity
     */
    public function search(array $options = []);
}
