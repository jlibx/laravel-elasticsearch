<?php


namespace Golly\Elastic\DSL;


use Golly\Elastic\Contracts\AggregationInterface;
use Golly\Elastic\Contracts\EndpointInterface;
use Golly\Elastic\Contracts\HighlightInterface;
use Golly\Elastic\Contracts\InnerHitInterface;
use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Contracts\SortInterface;
use Golly\Elastic\Contracts\SuggestInterface;
use Golly\Elastic\DSL\Endpoints\AggregationEndpoint;
use Golly\Elastic\DSL\Endpoints\EndpointFactory;
use Golly\Elastic\DSL\Endpoints\HighlightEndpoint;
use Golly\Elastic\DSL\Endpoints\InnerHitEndpoint;
use Golly\Elastic\DSL\Endpoints\SortEndpoint;
use Golly\Elastic\DSL\Endpoints\QueryEndpoint;
use Golly\Elastic\DSL\Endpoints\SuggestEndpoint;
use Golly\Elastic\DSL\Queries\BoolQuery;
use Golly\Elastic\Exceptions\ElasticException;

/**
 * Class Builder
 * @package Golly\Elastic\DSL
 */
class Builder
{
    use HasParams;

    /**
     * @var EndpointInterface[]
     */
    protected $endpoints = [];

    /**
     * @var bool
     */
    protected $trackTotalHits = true;

    /**
     * 从某个偏移点检索命中
     *
     * @var int
     */
    protected $from;

    /**
     * 查询数量
     *
     * @var int
     */
    protected $size;

    /**
     * 允许控制_source字段如何在每次命中时返回.
     * 默认情况下操作返回_source字段的内容
     *
     * @var
     */
    protected $source;

    /**
     * @var array
     */
    protected $storedFields;

    /**
     * @var array
     */
    protected $scriptFields;

    /**
     * 为每个命中提供解释，说明其得分是如何计算的。
     *
     * @var bool
     */
    protected $explain = false;

    /**
     * 返回每个搜索命中的版本。
     *
     * @var bool
     */
    protected $version = false;

    /**
     * 允许在跨多个索引搜索时, 为每个索引配置不同的提升级别多于一个索引。
     *
     *
     * @var array
     */
    protected $indicesBoost = [];

    /**
     * 排除那些_score小于min_score中指定的最小值的文档。
     *
     * @var int
     */
    protected $minScore = 0;

    protected $searchAfter;

    /**
     * @var
     */
    protected $scroll;

    /**
     * URI参数连同请求正文搜索。
     *
     * @var array
     */
    protected $uriParams = [];

    /**
     * Returns endpoint instance.
     *
     * @param string $type Endpoint type.
     *
     * @return EndpointInterface
     */
    public function getEndpoint(string $type)
    {
        if (!array_key_exists($type, $this->endpoints)) {
            $this->endpoints[$type] = EndpointFactory::get($type);
        }

        return $this->endpoints[$type];
    }

    /**
     * Destroys search endpoint.
     *
     * @param string $type Endpoint type.
     */
    public function destroyEndpoint(string $type)
    {
        unset($this->endpoints[$type]);
    }

    /**
     * Returns queries inside BoolQuery instance.
     *
     * @return BoolQuery
     */
    public function getBoolQuery()
    {
        /**
         * @var QueryEndpoint $endpoint
         */
        $endpoint = $this->getEndpoint(QueryEndpoint::NAME);

        return $endpoint->getBoolQuery();
    }

    /**
     * @param BoolQuery $query
     * @return $this
     */
    public function setBoolQuery(BoolQuery $query)
    {
        /**
         * @var QueryEndpoint $endpoint
         */
        $endpoint = $this->getEndpoint(QueryEndpoint::NAME);
        $endpoint->setBoolQuery($query);

        return $this;
    }

    /**
     * @param QueryInterface $query
     * @param string $boolType
     * @return $this
     */
    public function addQuery(QueryInterface $query, $boolType = BoolQuery::MUST)
    {
        /**
         * @var QueryEndpoint $endpoint
         */
        $endpoint = $this->getEndpoint(QueryEndpoint::NAME);
        $endpoint->addToBoolQuery($query, $boolType);

        return $this;
    }

    /**
     * Sets query endpoint parameters.
     *
     * @param array $params
     *
     * @return $this
     */
    public function setQueryParams(array $params)
    {
        $this->setEndpointParams(QueryEndpoint::NAME, $params);

        return $this;
    }

    /**
     * Adds sort to search.
     *
     * @param SortInterface $sort
     *
     * @return $this
     */
    public function addSort(SortInterface $sort)
    {
        $this->getEndpoint(SortEndpoint::NAME)->addContainer($sort);

        return $this;
    }

    /**
     * @return SortInterface[]
     */
    public function getSorts()
    {
        return $this->getEndpoint(SortEndpoint::NAME)->getContainers();
    }

    /**
     * @param HighlightInterface $highlight
     * @return $this
     */
    public function addHighlight(HighlightInterface $highlight)
    {
        $this->getEndpoint(HighlightEndpoint::NAME)->addContainer($highlight);

        return $this;
    }

    /**
     * @return HighlightInterface[]
     */
    public function getHighlights()
    {
        return $this->getEndpoint(HighlightEndpoint::NAME)->getContainers();
    }


    /**
     * Adds aggregation into search.
     *
     * @param AggregationInterface $aggregation
     *
     * @return $this
     */
    public function addAggregation(AggregationInterface $aggregation)
    {
        $this->getEndpoint(AggregationEndpoint::NAME)->addContainer($aggregation);

        return $this;
    }

    /**
     * Returns all aggregations.
     *
     * @return AggregationInterface[]
     */
    public function getAggregations()
    {
        return $this->getEndpoint(AggregationEndpoint::NAME)->getContainers();
    }

    /**
     * Adds inner hit into search.
     *
     * @param InnerHitInterface $innerHit
     *
     * @return $this
     */
    public function addInnerHit(InnerHitInterface $innerHit)
    {
        $this->getEndpoint(InnerHitEndpoint::NAME)->addContainer($innerHit);

        return $this;
    }

    /**
     * Returns all inner hits.
     *
     * @return InnerHitInterface[]
     */
    public function getInnerHits()
    {
        return $this->getEndpoint(InnerHitEndpoint::NAME)->getContainers();
    }

    /**
     * @param SuggestInterface $suggest
     * @return $this
     */
    public function addSuggest(SuggestInterface $suggest)
    {
        $this->getEndpoint(SuggestEndpoint::NAME)->addContainer($suggest);

        return $this;
    }

    /**
     * Returns all suggests.
     *
     * @return SuggestInterface[]
     */
    public function getSuggests()
    {
        return $this->getEndpoint(SuggestEndpoint::NAME)->getContainers();
    }

    /**
     * Sets parameters to the endpoint.
     *
     * @param string $endpointName
     * @param array $params
     *
     * @return $this
     */
    public function setEndpointParams(string $endpointName, array $params = [])
    {
        $endpoint = $this->getEndpoint($endpointName);
        $endpoint && $endpoint->setParams($params);

        return $this;
    }

    /**
     * @param int $from
     * @return $this
     */
    public function setFrom(int $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize(int $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExplain()
    {
        return $this->explain;
    }

    /**
     * @param bool $explain
     *
     * @return $this
     */
    public function setExplain(bool $explain)
    {
        $this->explain = $explain;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVersion()
    {
        return $this->version;
    }

    /**
     * @param bool $version
     *
     * @return $this
     */
    public function setVersion(bool $version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return array
     */
    public function getIndicesBoost()
    {
        return $this->indicesBoost;
    }

    /**
     * @param array $indicesBoost
     *
     * @return $this
     */
    public function setIndicesBoost(array $indicesBoost)
    {
        $this->indicesBoost = $indicesBoost;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinScore()
    {
        return $this->minScore;
    }

    /**
     * @param int $minScore
     *
     * @return $this
     */
    public function setMinScore(int $minScore)
    {
        $this->minScore = $minScore;

        return $this;
    }

    /**
     * @return string
     */
    public function getScroll()
    {
        return $this->scroll;
    }

    /**
     * @param string $scroll
     *
     * @return $this
     * @throws ElasticException
     */
    public function setScroll($scroll = '5m')
    {
        $this->scroll = $scroll;

        $this->addUriParam('scroll', $this->scroll);

        return $this;
    }

    /**
     * @param string $name
     * @param string|array|bool $value
     *
     * @return $this
     * @throws ElasticException
     */
    public function addUriParam(string $name, $value)
    {
        if (in_array($name, [
            'q',
            'df',
            'analyzer',
            'analyze_wildcard',
            'default_operator',
            'lenient',
            'explain',
            '_source',
            '_source_exclude',
            '_source_include',
            'stored_fields',
            'sort',
            'track_scores',
            'timeout',
            'terminate_after',
            'from',
            'size',
            'search_type',
            'scroll',
            'allow_no_indices',
            'ignore_unavailable',
            'typed_keys',
            'pre_filter_shard_size',
            'ignore_unavailable',
        ])) {
            $this->uriParams[$name] = $value;
        } else {
            throw new ElasticException(sprintf('Parameter %s is not supported.', $value));
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $output = [];
        foreach ($this->endpoints as $key => $endpoint) {
            if ($endpoint->normalize()) {
                $output['body'][$key] = $endpoint->normalize();
            }
        }

        $params = [
            'from' => 'from',
            'size' => 'size',
            'source' => '_source',
            'storedFields' => 'stored_fields',
            'scriptFields' => 'script_fields',
            'explain' => 'explain',
            'version' => 'version',
            'indicesBoost' => 'indices_boost',
            'minScore' => 'min_score',
            'searchAfter' => 'search_after',
            'trackTotalHits' => 'track_total_hits',
        ];

        foreach ($params as $field => $param) {
            if ($this->{$field}) {
                $output[$param] = $this->{$field};
            }
        }

        return $output;
    }

}
