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
    protected array $scripts = [];

    /**
     * @var bool
     */
    protected bool $supportNesting = false;

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string
     */
    protected string $field;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string|null
     */
    protected ?string $prefix;

    /**
     * @var AggregationInterface[]
     */
    protected array $aggregations = [];

    /**
     * AbstractAggregation constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
        $this->initName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return void
     */
    public function initName(): void
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


    abstract public function getArray(): array;

    /**
     * @return array
     */
    public function toArray(): array
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
    public function collectNestedAggregations(): array
    {
        $result = [];
        foreach ($this->aggregations as $aggregation) {
            $result[$aggregation->getName()] = $aggregation->toArray();
        }

        return $result;
    }

}
