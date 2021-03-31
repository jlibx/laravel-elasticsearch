<?php


namespace Golly\Elastic;

/**
 * Trait HasParams
 * @package Golly\Elastic
 */
trait HasParams
{

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param array $array
     * @return array
     */
    public function merge(array $array = [])
    {
        return array_merge($this->params, $array);
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addParam(string $key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }
}
