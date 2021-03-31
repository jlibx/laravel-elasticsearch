<?php


namespace Golly\Elastic\Aggregations;


use Golly\Elastic\Contracts\AggregationInterface;
use Golly\Elastic\HasParams;

/**
 * Class Aggregation
 * @package Golly\Elastic\Aggregations
 */
abstract class Aggregation implements AggregationInterface
{
    use HasParams;

    /**
     * @var array
     */
    protected $script = [];

    /**
     * @var bool
     */
    protected $supportNesting = false;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var string
     */
    protected $field = '';

    /**
     * @var AggregationInterface[]
     */
    protected $aggregations = [];

    /**
     * AbstractAggregation constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
        $this->setName($field);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $field
     */
    public function setName(string $field)
    {
        $array = [];
        if ($this->prefix) {
            $array[] = $this->prefix;
        }
        $array = array_merge($array, [
            $this->field,
            $this->type
        ]);

        $this->name = implode('_', $array);
    }


    abstract public function getArray();

    /**
     * @return array
     */
    public function toArray()
    {
        $array = $this->getArray();
        $result = [
            $this->type => is_array($array) ? $this->merge($array) : $array,
        ];

        if ($this->supportNesting) {
            $aggregations = $this->collectNestedAggregations();
            if ($aggregations) {
                $result['aggregations'] = $aggregations;
            }
        }

        return $result;
    }


    /**
     * @return array
     */
    public function collectNestedAggregations()
    {
        $result = [];
        foreach ($this->aggregations as $aggregation) {
            $result[$aggregation->getName()] = $aggregation->toArray();
        }

        return $result;
    }

}
