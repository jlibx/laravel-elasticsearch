<?php


namespace Golly\Elastic\DSL\Aggregations\Metrics;


/**
 * Class RateMetric
 * @package Golly\Elastic\DSL\Aggregations\Metrics
 */
class RateMetric extends Metric
{

    /**
     * @var string
     */
    protected $type = 'rate';

    /**
     * @var string
     */
    protected $unit;

    /**
     * RateMetric constructor.
     * @param string $field
     * @param string $unit
     */
    public function __construct(string $field, string $unit)
    {
        parent::__construct($field);

        $this->unit = $unit;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return [
            'field' => $this->field,
            'unit' => $this->unit
        ];
    }
}
