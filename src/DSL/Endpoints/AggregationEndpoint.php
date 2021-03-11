<?php


namespace Golly\Elastic\DSL\Endpoints;


use Golly\Elastic\Contracts\AggregationInterface;

/**
 * Class AggregationEndpoint
 * @package Golly\Elastic\DSL\Endpoints
 */
class AggregationEndpoint extends AbstractEndpoint
{

    const NAME = 'aggregations';

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
}
