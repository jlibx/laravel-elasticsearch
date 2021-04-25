<?php


namespace Golly\Elastic\Contracts;

/**
 * Interface QueryInterface
 * @package Golly\Elastic\Contracts
 */
interface QueryInterface extends Arrayable
{

    /**
     * @return string
     */
    public function getType();


    /**
     * @return mixed
     */
    public function getTypeValue();

}
