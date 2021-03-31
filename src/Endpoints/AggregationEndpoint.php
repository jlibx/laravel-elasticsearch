<?php


namespace Golly\Elastic\Endpoints;


use Golly\Elastic\Aggregations\Bucket\RangeAggregation;
use Golly\Elastic\Aggregations\Bucket\TermsAggregation;
use Golly\Elastic\Aggregations\Metric\AvgAggregation;
use Golly\Elastic\Aggregations\Metric\MaxAggregation;
use Golly\Elastic\Aggregations\Metric\MinAggregation;
use Golly\Elastic\Aggregations\Metric\StatsAggregation;
use Golly\Elastic\Aggregations\Metric\SumAggregation;
use Golly\Elastic\Contracts\AggregationInterface;

/**
 * Class AggregationEndpoint
 * @package Golly\Elastic\Endpoints
 */
class AggregationEndpoint extends Endpoint
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'aggregations';
    }

    /**
     * @return array
     */
    public function normalize()
    {
        $output = [];
        /**
         * @var AggregationInterface $aggregation
         */
        foreach ($this->containers as $aggregation) {
            $output[$aggregation->getName()] = $aggregation->toArray();
        }

        return $output;
    }

    /**
     * @param string $field
     * @param array $ranges
     * @return $this
     */
    public function addRangeBucket(string $field, array $ranges)
    {
        $aggregation = new RangeAggregation($field, $ranges);
        $this->addContainer($aggregation, $aggregation->getName());

        return $this;
    }

    /**
     * @param string $field
     * @param array $script
     * @return $this
     */
    public function addTermsBucket(string $field, array $script = [])
    {
        $aggregation = new TermsAggregation($field, $script);
        $this->addContainer($aggregation, $aggregation->getName());

        return $this;
    }

    /**
     * @param $field
     * @param $type
     * @return $this
     */
    public function addAggregation($field, $type)
    {
        $aggregation = null;
        switch ($type) {
            case 'stats':
                $aggregation = new StatsAggregation($field);
                break;
            case 'sum':
                $aggregation = new SumAggregation($field);
                break;
            case 'min':
                $aggregation = new MinAggregation($field);
                break;
            case 'max':
                $aggregation = new MaxAggregation($field);
                break;
            case 'avg':
                $aggregation = new AvgAggregation($field);
                break;
        }
        if ($aggregation) {
            $this->addContainer($aggregation, $aggregation->getName());
        }

        return $this;
    }

}
