<?php


namespace Golly\Elastic;


use Golly\Elastic\Contracts\EntityInterface;
use Illuminate\Support\Arr;

/**
 * Class Entity
 * @package Golly\Elastic
 */
class Entity implements EntityInterface
{

    /**
     * @var Reflection
     */
    protected $reflection;

    /**
     * @var bool
     */
    protected $snake = true;

    /**
     * @var array
     */
    protected $original = [];

    /**
     * @var array
     */
    protected $array = [];

    /**
     * @return array
     * @throws Exceptions\ElasticException
     */
    public static function mapping()
    {
        $self = new static();

        return $self->getReflection()->map($self);
    }

    /**
     * @param array $data
     * @return EntityInterface|Entity
     * @throws Exceptions\ElasticException
     */
    public static function instance(array $data = [])
    {
        if (empty($data)) {
            return new static();
        }

        return (new static())->toObject($data);
    }

    /**
     * array to this object
     *
     * @param array $data
     * @return EntityInterface|static
     * @throws Exceptions\ElasticException
     */
    public function toObject(array $data)
    {
        $this->original = $data;

        return $this->getReflection()->hydrate($data, $this);
    }


    /**
     * @param callable|null $filter
     * @return array
     * @throws Exceptions\ElasticException
     */
    public function toArray(callable $filter = null)
    {
        if (!$this->array) {
            $this->array = $this->getReflection()->extract($this, $this->snake);
        }
        if (is_null($filter)) {
            return $this->array;
        }

        return $filter($this->array);
    }

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array|mixed
     * @throws Exceptions\ElasticException
     */
    public function except(array $keys, callable $filter = null)
    {
        return Arr::except($this->toArray($filter), $keys);
    }

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array|mixed
     * @throws Exceptions\ElasticException
     */
    public function only(array $keys, callable $filter = null)
    {
        return Arr::only($this->toArray($filter), $keys);
    }

    /**
     * @param null $key
     * @return array|mixed
     */
    public function getOriginal($key = null)
    {
        return Arr::get($this->original, $key);
    }

    /**
     * @return Reflection
     */
    protected function getReflection()
    {
        if (!$this->reflection) {
            $this->reflection = new Reflection();
        }

        return $this->reflection;
    }
}
