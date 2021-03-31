<?php


namespace Golly\Elastic\Aggregations\Metric;


use Golly\Elastic\Aggregations\Aggregation;

/**
 * Class MetricAggregation
 * @package Golly\Elastic\Aggregations\Metric
 */
abstract class MetricAggregation extends Aggregation
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
