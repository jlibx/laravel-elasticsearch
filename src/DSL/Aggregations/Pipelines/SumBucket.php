<?php


namespace Golly\Elastic\DSL\Aggregations\Pipelines;


/**
 * Class SumBucket
 * @package Golly\Elastic\DSL\Aggregations\Pipelines
 */
class SumBucket extends Pipeline
{

    /**
     * @var string
     */
    protected $type = 'sum_bucket';

}
