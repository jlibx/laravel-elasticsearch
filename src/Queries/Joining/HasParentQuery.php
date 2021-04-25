<?php


namespace Golly\Elastic\Queries\Joining;


use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Queries\Query;

/**
 * Class HasParentQuery
 * @package Golly\Elastic\Queries\Joining
 */
class HasParentQuery extends Query
{

    /**
     * @var string
     */
    protected $parentType;

    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * HasParentQuery constructor.
     * @param string $parentType
     * @param QueryInterface $query
     * @param array $params
     */
    public function __construct(string $parentType, QueryInterface $query, array $params = [])
    {
        $this->parentType = $parentType;
        $this->query = $query;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'has_parent';
    }

    /**
     * @return array
     */
    public function getTypeValue()
    {
        return $this->merge([
            'parent_type' => $this->parentType,
            'query' => $this->query->toArray(),
        ]);
    }
}
