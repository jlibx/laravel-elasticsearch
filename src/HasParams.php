<?php
declare(strict_types=1);

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
    protected array $params = [];

    /**
     * @param array $array
     * @return array
     */
    public function merge(array $array = []): array
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
     * @return $this
     */
    public function setParams(array $params): static
    {
        $this->params = $params;

        return $this;
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addParam(string $key, mixed $value): static
    {
        $this->params[$key] = $value;

        return $this;
    }
}
