<?php


namespace Golly\Elastic\Queries\Geo;


use Golly\Elastic\Queries\Query;

/**
 * Class GeoShapeQuery
 * @package Golly\Elastic\Queries\Geo
 */
class GeoShapeQuery extends Query
{

    /**
     * @return string
     */
    public function getType()
    {
        return 'geo_shape';
    }


    public function getOutput()
    {
        // TODO: Implement output() method.
    }

}
