<?php


namespace Golly\Elastic\Queries\Compound;


use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Queries\Query;

/**
 * Class ConstantScoreQuery
 * @package Golly\Elastic\Queries\Compound
 */
class ConstantScoreQuery extends Query
{

    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * ConstantScoreQuery constructor.
     * @param QueryInterface $query
     * @param array $params
     */
    public function __construct(QueryInterface $query, array $params = [])
    {
        $this->query = $query;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'constant_score';
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return $this->merge([
            'filter' => $this->query->toArray()
        ]);
    }

}
