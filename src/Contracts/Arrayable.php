<?php


namespace Golly\Elastic\Contracts;

/**
 * Interface Arrayable
 * @package Golly\Elastic\Contracts
 */
interface Arrayable
{
    /**
     * @return array
     */
    public function toArray(): array;
}
