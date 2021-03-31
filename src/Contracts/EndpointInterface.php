<?php


namespace Golly\Elastic\Contracts;

/**
 * Interface EndpointInterface
 * @package Golly\Elastic\Contracts
 */
interface EndpointInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function normalize();
}
