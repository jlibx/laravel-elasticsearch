<?php


namespace Golly\Elastic\DSL\Aggregations\Pipelines;


use Golly\Elastic\DSL\Aggregations\AbstractAggregation;

/**
 * Class Pipeline
 * @package Golly\Elastic\DSL\Aggregations\Pipelines
 */
abstract class Pipeline extends AbstractAggregation
{

    /**
     * @var bool
     */
    protected $supportNesting = false;

    /**
     * @var string
     */
    protected $prefix = 'pipeline';

    /**
     * @var string
     */
    protected $bucketsPath;

    /**
     * Pipeline constructor.
     * @param string $field
     * @param string $bucketsPath
     */
    public function __construct(string $field, string $bucketsPath)
    {
        parent::__construct($field);

        $this->bucketsPath = $bucketsPath;
    }


    /**
     * @return array
     */
    public function getArray()
    {
        return [
            'buckets_path' => $this->bucketsPath
        ];
    }
}
