<?php


namespace Golly\Elastic\Endpoints;


use Golly\Elastic\Sorts\FieldSort;

/**
 * Class SortEndpoint
 * @package Golly\Elastic\Endpoints
 */
class SortEndpoint extends Endpoint
{

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'sort';
    }

    /**
     * @param string $field
     * @param string $direction
     */
    public function addFieldSort(string $field, string $direction = 'asc')
    {
        $this->addContainer(
            new FieldSort($field, $direction), $field
        );
    }

}
