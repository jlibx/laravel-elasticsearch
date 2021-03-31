<?php


namespace Golly\Elastic\Aggregations\Metric;


/**
 * Class MaxAggregation
 * @package Golly\Elastic\Aggregations\Metric
 */
class MaxAggregation extends StatsAggregation
{
    /**
     * @var string
     */
    protected $type = 'max';
}
