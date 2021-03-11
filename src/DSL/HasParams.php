<?php


namespace Golly\Elastic\DSL;


use Illuminate\Support\Arr;

/**
 * Trait HasParams
 * @package Golly\Elastic\DSL
 */
trait HasParams
{

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param $key
     * @return bool
     */
    public function hasParam($key): bool
    {
        return Arr::exists($this->params, $key);
    }

    /**
     * @param string|null $key
     * @param null $default
     * @return mixed
     */
    public function getParam(string $key = null, $default = null)
    {
        return Arr::get($this->params, $key, $default);
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function addParam(string $key, $value)
    {
        Arr::set($this->params, $key, $value);

        return $this;
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
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @param array $data
     * @return array
     */
    public function merge(array $data = []): array
    {
        return array_merge($this->params, $data);
    }

}
