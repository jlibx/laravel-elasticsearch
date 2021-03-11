<?php


namespace Golly\Elastic\DSL\Aggregations\Buckets;


use Golly\Elastic\DSL\Aggregations\AbstractAggregation;

/**
 * Class Bucket
 * @package Golly\Elastic\DSL\Aggregations\Buckets
 */
abstract class AbstractBucket extends AbstractAggregation
{
    /**
     * @var bool
     */
    protected $supportNesting = true;

    /**
     * @var string
     */
    protected $prefix = 'bucket';

}
