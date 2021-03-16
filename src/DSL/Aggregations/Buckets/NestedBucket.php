<?php


namespace Golly\Elastic\DSL\Aggregations\Buckets;


/**
 * Class NestedBucket
 * @package Golly\Elastic\DSL\Aggregations\Buckets
 */
class NestedBucket extends Bucket
{

    /**
     * @var string
     */
    protected $type = 'nested';

    /**
     * @var string
     */
    protected $path;

    /**
     * NestedBucket constructor.
     * @param string $field
     * @param string $path
     */
    public function __construct(string $field, string $path)
    {
        parent::__construct($field);

        $this->path = $path;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return [
            'path' => $this->path
        ];
    }
}
