<?php


namespace Golly\Elastic\DSL\Aggregations\Metrics;


/**
 * Class MaxMetric
 * @package Golly\Elastic\DSL\Aggregations\Metrics
 */
class MaxMetric extends StatsMetric
{

    /**
     * @var string
     */
    protected $type = 'max';

}
