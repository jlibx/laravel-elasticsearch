<?php


namespace Golly\Elastic\Queries\TermLevel;


use Golly\Elastic\Queries\Query;

/**
 * Class ExistsQuery
 * @package Golly\Elastic\Queries\TermLevel
 */
class ExistsQuery extends Query
{

    /**
     * ExistsQuery constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'exists';
    }

    /**
     * @return mixed|string
     */
    public function getOutput()
    {
        return $this->field;
    }
}
