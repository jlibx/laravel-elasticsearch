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
    public function getName()
    {
        return 'sort';
    }

    /**
     * @param string $field
     * @param string $direction
     */
    public function addFieldSort(string $field, $direction = 'asc')
    {
        $this->addContainer(
            new FieldSort($field, $direction), $field
        );
    }

}
