<?php


namespace Golly\Elastic\DSL\Queries;


use Golly\Elastic\Contracts\QueryInterface;

/**
 * Class ConstantScoreQuery
 * @package Golly\Elastic\DSL\Queries
 */
class ConstantScoreQuery extends AbstractQuery
{

    protected $type = 'constant_score';

    /**
     * ConstantScoreQuery constructor.
     * @param QueryInterface $query
     * @param array $params
     */
    public function __construct(QueryInterface $query, array $params = [])
    {
        $this->value = $query;
        $this->setParams($params);
    }

    /**
     * @return array
     */
    public function output()
    {
        return $this->merge([
            'filter' => $this->value->toArray(),
        ]);
    }
}
