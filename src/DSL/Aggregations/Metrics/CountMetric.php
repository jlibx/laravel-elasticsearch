<?php


namespace Golly\Elastic\DSL\Aggregations\Metrics;


/**
 * Class CountMetric
 * @package Golly\Elastic\DSL\Aggregations\Metrics
 */
class CountMetric extends StatsMetric
{
    /**
     * @var string
     */
    protected $type = 'value_count';

}
