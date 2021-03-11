<?php


namespace Golly\Elastic\DSL\Aggregations\Metrics;


use Golly\Elastic\DSL\Aggregations\AbstractAggregation;

/**
 * Class Metric
 * @package Golly\Elastic\DSL\Aggregations\Metrics
 */
abstract class Metric extends AbstractAggregation
{

    /**
     * @var bool
     */
    protected $supportNesting = false;

    /**
     * @var string
     */
    protected $prefix = 'metric';

}
