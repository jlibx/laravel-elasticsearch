<?php


namespace Golly\Elastic\Aggregations\Metric;

/**
 * Class StatsAggregation
 * @package Golly\Elastic\Aggregations\Metric
 */
class StatsAggregation extends MetricAggregation
{
    /**
     * The stats that are returned consist of: min, max, sum, count and avg.
     *
     * @var string
     */
    protected $type = 'stats';

    /**
     * @return array
     */
    public function getArray()
    {
        $output = [];
        if ($this->field) {
            $output['field'] = $this->field;
        }
        if ($this->script) {
            $output['script'] = $this->script;
        }

        return $output;
    }
}
