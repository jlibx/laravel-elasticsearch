<?php


namespace Golly\Elastic\Hydrate;

use Golly\Elastic\Contracts\EntityInterface;
use Illuminate\Support\Arr;

/**
 * Class Entity
 * @package Golly\Elastic\Hydrate
 */
class Entity implements EntityInterface
{

    /**
     * 原始数据
     *
     * @var array
     */
    protected $original = [];

    /**
     * 已转化数据
     *
     * @var array
     */
    protected $array = [];

    /**
     * @param bool $relation
     * @return array
     */
    public static function mapping($relation = true)
    {
        $self = new static();

        return $self->getReflection()->map($self);
    }

    /**
     * @param array $data
     * @param bool $original
     * @return EntityInterface|static
     */
    public static function instance(array $data, $original = true)
    {
        return (new static())->toObject($data, $original);
    }

    /**
     * array to this object
     *
     * @param array $data
     * @param bool $original
     * @return EntityInterface
     */
    public function toObject(array $data, $original = true)
    {
        if ($original) {
            $this->original = $data;
        }
        $this->array = [];

        return $this->getReflection()->hydrate($data, $this);
    }


    /**
     * @param callable|null $filter
     * @param string $format
     * @return array
     */
    public function toArray(callable $filter = null, $format = 'snake')
    {
        if (!isset($this->array[$format])) {
            $this->array[$format] = $this->getReflection()->extract($this, $format);
        }
        if (is_null($filter)) {
            return $this->array[$format];
        }

        return $filter($this->array[$format]);
    }

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function except(array $keys, callable $filter = null)
    {
        return Arr::except($this->toArray($filter), $keys);
    }


    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function only(array $keys, callable $filter = null)
    {
        return Arr::only($this->toArray($filter), $keys);
    }


    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getOriginal($key = null, $default = null)
    {
        if ($key) {
            return Arr::get($this->original, $key, $default);
        }

        return $this->original;
    }

    /**
     * @return Reflection
     */
    protected function getReflection()
    {
        return app(Reflection::class);
    }

}
