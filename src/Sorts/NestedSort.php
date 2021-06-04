<?php


namespace Golly\Elastic\Sorts;


use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Contracts\SortInterface;
use Golly\Elastic\HasParams;

/**
 * Class NestedSort
 * @package Golly\Elastic\Sorts
 */
class NestedSort implements SortInterface
{
    use HasParams;

    /**
     * @var string
     */
    protected string $path;

    /**
     * @var QueryInterface|null
     */
    protected ?QueryInterface $filter;

    /**
     * @var QueryInterface|null
     */
    protected ?QueryInterface $nestedFilter;

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
    public function toArray(): array
    {
        $output = [
            'path' => $this->path,
        ];
        if ($this->filter) {
            $output['filter'] = $this->filter->toArray();
        }
        if ($this->nestedFilter) {
            $output['nested'] = $this->nestedFilter->toArray();
        }

        return $this->merge($output);
    }
}
