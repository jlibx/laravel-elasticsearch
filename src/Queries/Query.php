<?php


namespace Golly\Elastic\Queries;

use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\HasParams;

/**
 * Class Query
 * @package Golly\Elastic\Queries
 */
abstract class Query implements QueryInterface
{
    use HasParams;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return mixed
     */
    abstract public function getOutput();

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            $this->getType() => $this->getOutput()
        ];
    }
}
