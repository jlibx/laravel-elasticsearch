<?php


namespace Golly\Elastic\Queries\TermLevel;


use Golly\Elastic\Queries\Query;

/**
 * Class RangeQuery
 * @package Golly\Elastic\Queries\TermLevel
 */
class RangeQuery extends Query
{

    const LT = 'lt';
    const GT = 'gt';
    const LTE = 'lte';
    const GTE = 'gte';

    /**
     * RangeQuery constructor.
     * @param $field
     * @param array $params
     */
    public function __construct($field, array $params = [])
    {
        $this->field = $field;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'range';
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return [
            $this->field => $this->params,
        ];
    }
}
