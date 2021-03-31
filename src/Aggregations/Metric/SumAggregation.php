<?php


namespace Golly\Elastic\Aggregations\Metric;


/**
 * Class SumAggregation
 * @package Golly\Elastic\Aggregations\Metric
 */
class SumAggregation extends StatsAggregation
{
    /**
     * @var string
     */
    protected $type = 'sum';

}
