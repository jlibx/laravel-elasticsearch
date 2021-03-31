<?php


namespace Golly\Elastic\Contracts;


/**
 * Interface AggregationInterface
 * @package Golly\Elastic\Contracts
 */
interface AggregationInterface extends Arrayable
{

    /**
     * @return string
     */
    public function getName();
}
