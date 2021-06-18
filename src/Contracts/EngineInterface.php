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
     * Perform the given search on the engine.
     *
     * @param array $options
     * @return ElasticEntity
     */
    public function search(array $options = []): ElasticEntity;


    /**
     * @param array $params
     * @return array
     */
    public function bulk(array $params): array;

}
