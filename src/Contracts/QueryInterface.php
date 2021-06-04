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
    public function getType(): string;


    /**
     * @return array
     */
    public function getTypeValue(): array;

}
