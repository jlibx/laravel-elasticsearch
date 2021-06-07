<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\Joining;

use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Queries\Query;

/**
 * Class NestedQuery
 * @package Golly\Elastic\Queries\Joining
 */
class NestedQuery extends Query
{
    /**
     * @var string
     */
    protected string $path;

    /**
     * @var QueryInterface
     */
    protected QueryInterface $query;

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
     * @return string
     */
    public function getType(): string
    {
        return 'nested';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return $this->merge([
            'path' => $this->path,
            'query' => $this->query->toArray(),
        ]);
    }
}
