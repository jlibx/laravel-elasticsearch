<?php


namespace Golly\Elastic\Aggregations\Bucket;


use Golly\Elastic\Aggregations\Aggregation;

/**
 * Class BucketAggregation
 * @package Golly\Elastic\Aggregations\Buckets
 */
abstract class BucketAggregation extends Aggregation
{
    /**
     * @var bool
     */
    protected bool $supportNesting = true;

    /**
     * @var string
     */
    protected string $prefix = 'bucket';

}
