<?php


namespace Golly\Elastic\DSL\Queries;


use stdClass;

/**
 * Class MatchAllQuery
 * @package Golly\Elastic\DSL\Queries
 */
class MatchAllQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'match_all';

    /**
     * MatchAllQuery constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @return array|stdClass
     */
    public function output()
    {
        $params = $this->getParams();

        return $params ?? new stdClass();
    }


}
