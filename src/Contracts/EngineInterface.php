<?php
declare(strict_types=1);

namespace Golly\Elastic\Contracts;

use Golly\Elastic\Hydrate\ElasticEntity;
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
     * @return bool
     */
    public function update(Collection $models): bool;

    /**
     * Remove the given model from the index.
     *
     * @param Collection $models
     * @return bool
     */
    public function delete(Collection $models): bool;

    /**
     * Perform the given search on the engine.
     *
     * @param array $options
     * @return ElasticEntity
     */
    public function search(array $options = []): ElasticEntity;
}
