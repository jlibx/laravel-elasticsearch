<?php


namespace Golly\Elastic\Queries;


use stdClass;

/**
 * Class MatchAllQuery
 * @package Golly\Elastic\Queries
 */
class MatchAllQuery extends Query
{

    /**
     * MatchAllQuery constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'match_all';
    }

    /**
     * @return array|stdClass
     */
    public function getOutput()
    {
        return $this->params ?: new stdClass();
    }

}
