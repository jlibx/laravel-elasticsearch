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
    protected string $type = 'stats';

    /**
     * @return array
     */
    public function getArray(): array
    {
        $output = [];
        if ($this->field) {
            $output['field'] = $this->field;
        }
        if ($this->scripts) {
            $output['script'] = $this->scripts;
        }

        return $output;
    }
}
