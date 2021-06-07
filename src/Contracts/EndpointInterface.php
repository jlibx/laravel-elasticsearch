<?php
declare(strict_types=1);

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
     * @return array
     */
    public function normalize(): array;
}
