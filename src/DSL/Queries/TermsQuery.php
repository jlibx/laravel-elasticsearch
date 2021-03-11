<?php


namespace Golly\Elastic\DSL\Queries;


/**
 * Class TermsQuery
 * @package Golly\Elastic\DSL\Queries
 */
class TermsQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'terms';

    /**
     * TermsQuery constructor.
     * @param string $field
     * @param array $terms
     * @param array $params
     */
    public function __construct(string $field, array $terms, array $params = [])
    {
        $this->field = $field;
        $this->value = $terms;
        $this->setParams($params);
    }


    /**
     * @return array
     */
    public function output()
    {
        $query = [
            $this->field => $this->value,
        ];

        return $this->merge($query);
    }
}
