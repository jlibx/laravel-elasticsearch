<?php


namespace Golly\Elastic\DSL\Aggregations\Metrics;


/**
 * Class SumMetric
 * @package Golly\Elastic\DSL\Aggregations\Metrics
 */
class SumMetric extends StatsMetric
{

    /**
     * @var string
     */
    protected $type = 'sum';

}
