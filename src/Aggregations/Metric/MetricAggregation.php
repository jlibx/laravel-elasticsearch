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
    protected bool $supportNesting = false;

    /**
     * @var string
     */
    protected string $prefix = 'metric';

}
