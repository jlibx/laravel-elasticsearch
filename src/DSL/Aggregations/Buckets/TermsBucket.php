<?php


namespace Golly\Elastic\DSL\Aggregations\Buckets;

/**
 * Class TermsBucket
 * @package Golly\Elastic\DSL\Aggregations\Buckets
 */
class TermsBucket extends AbstractBucket
{

    /**
     * @var string
     */
    protected $type = 'terms';

    /**
     * TermsBucket constructor.
     * @param string $field
     * @param array|null $script
     */
    public function __construct(string $field, array $script = null)
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
        return array_filter(
            [
                'field' => $this->field,
                'script' => $this->script,
            ]
        );

    }
}
