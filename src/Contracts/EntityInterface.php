<?php


namespace Golly\Elastic\Contracts;

/**
 * Interface EntityInterface
 * @package Golly\Elastic\Contracts
 */
interface EntityInterface
{
    /**
     * @param array $data
     * @return static
     */
    public static function instance(array $data);

    /**
     * @param bool $relation
     * @return array
     */
    public static function mapping($relation = true);

    /**
     * @param array $data
     * @return static
     */
    public function toObject(array $data);

    /**
     * @param callable|null $filter
     * @return array
     */
    public function toArray(callable $filter = null);

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array|mixed
     */
    public function except(array $keys, callable $filter = null);

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array|mixed
     */
    public function only(array $keys, callable $filter = null);

    /**
     * @param string|null $key
     * @return array|mixed
     */
    public function getOriginal($key = null);
}
