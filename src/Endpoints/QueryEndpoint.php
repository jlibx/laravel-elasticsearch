<?php
declare(strict_types=1);

namespace Golly\Elastic\Endpoints;

use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Queries\Compound\BoolQuery;
use Golly\Elastic\Queries\FullText\MatchQuery;
use Golly\Elastic\Queries\TermLevel\ExistsQuery;
use Golly\Elastic\Queries\TermLevel\RangeQuery;
use Golly\Elastic\Queries\TermLevel\TermQuery;
use Golly\Elastic\Queries\TermLevel\TermsQuery;
use Golly\Elastic\Queries\TermLevel\WildcardQuery;

/**
 * Class QueryEndpoint
 * @package Golly\Elastic\Endpoints
 */
class QueryEndpoint extends Endpoint
{

    /**
     * @var string|null
     */
    protected ?string $relation;

    /**
     * @var BoolQuery
     */
    protected BoolQuery $boolQuery;

    /**
     * QueryEndpoint constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->boolQuery = new BoolQuery($params);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'query';
    }

    /**
     * @return array
     */
    public function normalize(): array
    {
        if (!$this->boolQuery) {
            return [];
        }

        return $this->boolQuery->toArray();
    }

    /**
     * @param string $relation
     * @return $this
     */
    public function setRelation(string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @return BoolQuery
     */
    public function getBoolQuery(): BoolQuery
    {
        return $this->boolQuery;
    }


    /**
     * @param BoolQuery $query
     * @return $this
     */
    public function setBoolQuery(BoolQuery $query): self
    {
        $this->boolQuery = $query;

        return $this;
    }

    /**
     * @param QueryInterface $query
     * @param string $boolType
     * @return $this
     */
    public function addToBoolQuery(QueryInterface $query, string $boolType = 'must'): self
    {
        $this->boolQuery->addQuery($query, $boolType);

        return $this;
    }

    /**
     * @param string $field
     * @param string $boolType
     * @return $this
     */
    public function addExistsToBoolQuery(string $field, string $boolType): self
    {
        $field = $this->toRelationField($field);
        $this->addToBoolQuery(new ExistsQuery($field), $boolType);

        return $this;
    }

    /**
     * @param string $field
     * @param array $values
     * @param string $boolType
     * @return $this
     */
    public function addTermsToBoolQuery(string $field, array $values, string $boolType): self
    {
        $field = $this->toRelationField($field);
        $this->addToBoolQuery(new TermsQuery($field, $values), $boolType);

        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @param string $boolType
     * @return $this
     */
    public function addOpticalToBoolQuery(string $field, string $operator, mixed $value, string $boolType): self
    {
        $field = $this->toRelationField($field);
        if ($query = $this->toQuery($field, $operator, $value)) {
            $this->addToBoolQuery($query, $boolType);
        }

        return $this;
    }

    /**
     * @param string $field
     * @param int|float $min
     * @param int|float $max
     * @param string $boolType
     * @return $this
     */
    public function addBetweenToBoolQuery(string $field, int|float $min, int|float $max, string $boolType): self
    {
        $field = $this->toRelationField($field);
        $rQuery = (new RangeQuery($field))
            ->setGteValue($min)
            ->setLteValue($max);
        $this->addToBoolQuery($rQuery, $boolType);

        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return QueryInterface|null
     */
    protected function toQuery(string $field, string $operator, mixed $value): ?QueryInterface
    {
        $field = $this->toRelationField($field);

        return match ($operator) {
            '=', '!=', '<>' => new TermQuery($field, $value),
            '>' => (new RangeQuery($field))->setGtValue($value),
            '>=' => (new RangeQuery($field))->setGteValue($value),
            '<' => (new RangeQuery($field))->setLtValue($value),
            '<=' => (new RangeQuery($field))->setLteValue($value),
            'match' => new MatchQuery($field, $value),
            'like', 'wildcard' => new WildcardQuery($field, $value),
            default => null,
        };
    }


    /**
     * @param string $field
     * @return string
     */
    protected function toRelationField(string $field): string
    {
        if (!$this->relation || str_starts_with($field, $this->relation . '.')) {
            return $field;
        }

        return $this->relation . '.' . $field;
    }
}
