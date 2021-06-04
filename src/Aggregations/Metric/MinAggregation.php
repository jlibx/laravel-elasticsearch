<?php


namespace Golly\Elastic\Aggregations\Metric;


/**
 * Class MinAggregation
 * @package Golly\Elastic\Aggregations\Metric
 */
class MinAggregation extends StatsAggregation
{

    /**
     * @var string
     */
    protected string $type = 'min';
}
