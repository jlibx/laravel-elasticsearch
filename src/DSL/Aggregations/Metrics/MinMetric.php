<?php


namespace Golly\Elastic\DSL\Aggregations\Metrics;

/**
 * Class MinMetric
 * @package Golly\Elastic\DSL\Aggregations\Metrics
 */
class MinMetric extends StatsMetric
{

    /**
     * @var string
     */
    protected $type = 'min';

}
