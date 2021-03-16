<?php


namespace Golly\Elastic\DSL\Aggregations\Buckets;


use Golly\Elastic\DSL\Aggregations\AbstractAggregation;

/**
 * Class Bucket
 * @package Golly\Elastic\DSL\Aggregations\Buckets
 */
abstract class Bucket extends AbstractAggregation
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
