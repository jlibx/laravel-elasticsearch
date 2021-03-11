<?php


namespace Golly\Elastic\DSL\Queries;


/**
 * Class RangeQuery
 * @package Golly\Elastic\DSL\Queries
 */
class RangeQuery extends AbstractQuery
{
    const LT = 'lt';
    const GT = 'gt';
    const LTE = 'lte';
    const GTE = 'gte';

    /**
     * @var string
     */
    protected $type = 'range';

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
     * @return array
     */
    public function output()
    {
        return [
            $this->field => $this->params,
        ];
    }
}
