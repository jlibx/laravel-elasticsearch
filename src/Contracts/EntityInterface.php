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
     * @param boolean $original
     * @return static
     */
    public static function instance(array $data, $original = true);

    /**
     * @param array $data
     * @param boolean $original
     * @return static
     */
    public function toObject(array $data, $original = true);

    /**
     * @param callable|null $filter
     * @param string $format
     * @return array
     */
    public function toArray(callable $filter = null, string $format = 'snake');

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function except(array $keys, callable $filter = null);

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function only(array $keys, callable $filter = null);

    /**
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getOriginal($key = null, $default = null);
}
