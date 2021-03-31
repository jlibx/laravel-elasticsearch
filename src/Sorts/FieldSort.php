<?php


namespace Golly\Elastic\Sorts;


use Golly\Elastic\Contracts\SortInterface;
use Golly\Elastic\HasParams;

/**
 * Class FieldSort
 * @package Golly\Elastic\Sorts
 */
class FieldSort implements SortInterface
{
    use HasParams;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $direction;

    /**
     * FieldSort constructor.
     * @param string $field
     * @param string $direction
     * @param array $params
     */
    public function __construct(string $field, string $direction = 'asc', array $params = [])
    {
        $this->field = $field;
        $this->direction = $direction;
        $this->setParams($params);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $this->addParam('order', $this->direction);

        return [
            $this->field => $this->params,
        ];
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }
}
