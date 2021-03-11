<?php


namespace Golly\Elastic\DSL\Sorts;


use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Contracts\SortInterface;
use Golly\Elastic\DSL\HasParams;

/**
 * Class NestedSort
 * @package Golly\Elastic\DSL\Sorts
 */
class NestedSort implements SortInterface
{
    use HasParams;

    /**
     * @var string
     */
    protected $type = 'nested';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var QueryInterface|null
     */
    protected $filter;

    /**
     * @var QueryInterface
     */
    protected $nestedFilter;

    /**
     * NestedSort constructor.
     * @param string $path
     * @param QueryInterface|null $filter
     * @param array $params
     */
    public function __construct(string $path, QueryInterface $filter = null, array $params = [])
    {
        $this->path = $path;
        $this->filter = $filter;
        $this->setParams($params);
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $output = [
            'path' => $this->path,
        ];
        if ($this->filter) {
            $output['filter'] = $this->filter->toArray();
        }
        if ($this->nestedFilter) {
            $output[$this->type] = $this->nestedFilter->toArray();
        }

        return $this->merge($output);
    }

    /**
     * @param QueryInterface $nestedFilter
     *
     * @return $this
     */
    public function setNestedFilter(QueryInterface $nestedFilter)
    {
        $this->nestedFilter = $nestedFilter;

        return $this;
    }

}
