<?php


namespace Golly\Elastic\DSL\Endpoints;

use Golly\Elastic\Contracts\SortInterface;

/**
 * Class SortEndpoint
 * @package Golly\Elastic\DSL\Endpoints
 */
class SortEndpoint extends AbstractEndpoint
{
    /**
     * Endpoint name
     */
    const NAME = 'sort';

    /**
     * @return array
     */
    public function normalize()
    {
        $output = [];
        /**
         * @var SortInterface $sort
         */
        foreach ($this->containers as $sort) {
            $output[] = $sort->toArray();
        }

        return $output;
    }

}
