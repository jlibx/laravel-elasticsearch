<?php


namespace Golly\Elastic\DSL\Queries;


/**
 * Class ExistsQuery
 * @package Golly\Elastic\DSL\Queries
 */
class ExistsQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'exists';

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
    public function output()
    {
        return $this->field;
    }

}
