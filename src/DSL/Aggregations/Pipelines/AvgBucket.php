<?php


namespace Golly\Elastic\DSL\Aggregations\Pipelines;


/**
 * Class AvgBucket
 * @package Golly\Elastic\DSL\Aggregations\Pipelines
 */
class AvgBucket extends Pipeline
{

    /**
     * @var string
     */
    protected $type = 'avg_bucket';

}
