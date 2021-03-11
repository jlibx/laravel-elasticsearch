<?php


namespace Golly\Elastic\DSL\Aggregations\Buckets;


/**
 * Class ChildrenBucket
 * @package Golly\Elastic\DSL\Aggregations\Buckets
 */
class ChildrenBucket extends AbstractBucket
{

    /**
     * @var string
     */
    protected $type = 'children';

    /**
     * @var string
     */
    protected $children;


    /**
     * ChildrenBucket constructor.
     * @param string $field
     * @param string $children
     */
    public function __construct(string $field, string $children)
    {
        parent::__construct($field);

        $this->children = $children;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return [
            'type' => $this->children
        ];
    }
}
