<?php
declare(strict_types=1);

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
    public function getType(): string
    {
        return 'geo_shape';
    }


    public function getTypeValue(): array
    {
        return [];
    }

}
