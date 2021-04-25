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
     * @param array ranges
     * @return $this
     */
    public function setRanges(array $ranges)
    {
        foreach ($ranges as $key => $value) {
            $this->addParam($key, $value);
        }

        return $this;
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
    public function getTypeValue()
    {
        return [
            $this->field => $this->params,
        ];
    }
}
