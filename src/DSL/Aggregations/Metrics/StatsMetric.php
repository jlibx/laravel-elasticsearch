<?php


namespace Golly\Elastic\DSL\Aggregations\Metrics;


/**
 * Class StatsMetric
 * @package Golly\Elastic\DSL\Aggregations\Metrics
 */
class StatsMetric extends Metric
{
    /**
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
