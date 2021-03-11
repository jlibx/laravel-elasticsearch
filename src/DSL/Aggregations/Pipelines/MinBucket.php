<?php


namespace Golly\Elastic\DSL\Aggregations\Pipelines;


/**
 * Class MinBucket
 * @package Golly\Elastic\DSL\Aggregations\Pipelines
 */
class MinBucket extends Pipeline
{

    /**
     * @var string
     */
    protected $type = 'min_bucket';

}
