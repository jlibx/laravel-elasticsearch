<?php


namespace Golly\Elastic\DSL\Aggregations\Metrics;


/**
 * Class AvgMetric
 * @package Golly\Elastic\DSL\Aggregations\Metrics
 */
class AvgMetric extends StatsMetric
{
    /**
     * @var string
     */
    protected $type = 'avg';

}
