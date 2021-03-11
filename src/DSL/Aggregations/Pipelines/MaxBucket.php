<?php


namespace Golly\Elastic\DSL\Aggregations\Pipelines;

/**
 * Class MaxBucket
 * @package Golly\Elastic\DSL\Aggregations\Pipelines
 */
class MaxBucket extends Pipeline
{

    /**
     * @var string
     */
    protected $type = 'max_bucket';
}
