<?php


namespace Golly\Elastic\DSL\InnerHits;


use Golly\Elastic\Contracts\InnerHitInterface;
use Golly\Elastic\DSL\HasName;
use Golly\Elastic\DSL\HasParams;
use stdClass;

/**
 * Class NestedInnerHit
 * @package Golly\Elastic\DSL\InnerHits
 */
class NestedInnerHit implements InnerHitInterface
{
    use HasParams, HasName;

    /**
     * @return array
     */
    public function toArray()
    {
        return [];
    }
}
