<?php
declare(strict_types=1);

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
    public function getName(): string;
}
