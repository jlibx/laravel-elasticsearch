<?php


namespace Golly\Elastic\DSL\Queries;


use Golly\Elastic\Contracts\QueryInterface;

/**
 * Class NestedQuery
 * @package Golly\Elastic\DSL\Queries
 */
class NestedQuery extends AbstractQuery
{
    /**
     * @var string
     */
    protected $type = 'nested';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * NestedQuery constructor.
     * @param string $path
     * @param QueryInterface $query
     * @param array $params
     */
    public function __construct(string $path, QueryInterface $query, array $params = [])
    {
        $this->path = $path;
        $this->query = $query;
        $this->setParams($params);
    }

    /**
     * @return array
     */
    public function output()
    {
        return $this->merge([
            'path' => $this->path,
            'query' => $this->query->toArray(),
        ]);
    }
}
