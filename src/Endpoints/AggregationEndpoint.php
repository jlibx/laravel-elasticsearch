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
    public function getName(): string
    {
        return 'aggregations';
    }

    /**
     * @return array|null
     */
    public function normalize(): ?array
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
    public function addRangeBucket(string $field, array $ranges): static
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
    public function addTermsBucket(string $field, array $script = []): static
    {
        $aggregation = new TermsAggregation($field, $script);
        $this->addContainer($aggregation, $aggregation->getName());

        return $this;
    }

    /**
     * @param string $field
     * @param string $type
     * @return $this
     */
    public function addAggregation(string $field, string $type): static
    {
        $aggregation = match ($type) {
            'stats' => new StatsAggregation($field),
            'sum' => new SumAggregation($field),
            'min' => new MinAggregation($field),
            'max' => new MaxAggregation($field),
            'avg' => new AvgAggregation($field),
            default => null,
        };
        if ($aggregation) {
            $this->addContainer($aggregation, $aggregation->getName());
        }

        return $this;
    }

}
