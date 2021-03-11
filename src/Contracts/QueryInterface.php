<?php


namespace Golly\Elastic\Contracts;

/**
 * Interface QueryInterface
 * @package Golly\Elastic\Contracts
 */
interface QueryInterface extends Arrayable
{
    /**
     * @param array $params
     * @return void
     */
    public function setParams(array $params);


}
