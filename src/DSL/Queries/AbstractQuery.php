<?php


namespace Golly\Elastic\DSL\Queries;


use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\DSL\HasParams;

/**
 * Class AbstractQuery
 * @package Golly\Elastic\DSL\Queries
 */
abstract class AbstractQuery implements QueryInterface
{
    use HasParams;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string|array|QueryInterface
     */
    protected $value;

    abstract function output();

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            $this->type => $this->output()
        ];
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
