<?php


namespace Golly\Elastic\DSL\Sorts;


use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Contracts\SortInterface;
use Golly\Elastic\DSL\HasParams;
use stdClass;

/**
 * Class FieldSort
 * @package Golly\Elastic\DSL\Sorts
 */
class FieldSort implements SortInterface
{
    use HasParams;

    const ASC = 'asc';
    const DESC = 'desc';

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $order;

    /**
     * @var QueryInterface
     */
    protected $nestedFilter;

    /**
     * FieldSort constructor.
     * @param string $field
     * @param string|null $order
     * @param array $params
     */
    public function __construct(string $field, string $order = null, array $params = [])
    {
        $this->field = $field;
        $this->order = $order;
        $this->setParams($params);
    }


    /**
     * @return array
     */
    public function toArray()
    {
        if ($this->order) {
            $this->addParam('order', $this->order);
        }

        if ($this->nestedFilter) {
            $this->addParam('nested', $this->nestedFilter->toArray());
        }

        return [
            $this->field => $this->params ?: new stdClass(),
        ];
    }
}
