<?php


namespace Golly\Elastic\Aggregations\Bucket;

/**
 * Class TermsAggregation
 * @package Golly\Elastic\Aggregations\Buckets
 */
class TermsAggregation extends BucketAggregation
{
    /**
     * @var string
     */
    protected $type = 'terms';

    /**
     * TermsBucket constructor.
     * @param string $field
     * @param array $script
     */
    public function __construct(string $field, array $script = [])
    {
        parent::__construct($field);

        $this->field = $field;
        $this->script = $script;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return array_filter([
            'field' => $this->field,
            'script' => $this->script,
        ]);
    }

}
