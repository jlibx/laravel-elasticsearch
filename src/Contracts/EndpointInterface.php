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
    public function getName(): string;

    /**
     * @return array|null
     */
    public function normalize(): ?array;
}
