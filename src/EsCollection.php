<?php
declare(strict_types=1);

namespace Kabunx\LaravelElasticsearch;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Pagination\LengthAwarePaginator;
use IteratorAggregate;
use Kabunx\LaravelElasticsearch\Contracts\EsEntityInterface;
use Kabunx\LaravelElasticsearch\Hydrate\EsEntity;


/**
 * 将查询结果映射为ES集合
 */
class EsCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * 整个搜索请求花费时间（毫秒）
     *
     * @var float
     */
    public float $took = 0;

    /**
     * @var bool
     */
    public bool $timedOut = false;

    /**
     * @var int
     */
    public int $total = 0;

    /**
     * @var float|null
     */
    public ?float $maxScore = null;

    /**
     * @var array|EsEntity[]
     */
    public array $items = [];

    /**
     * @var array
     */
    public array $aggregations = [];

    /**
     * @param EsEntityInterface $entity
     * @param array $data
     * @return static
     */
    public static function make(EsEntityInterface $entity, array $data): static
    {
        $instance = new static();
        $instance->took = $data['took'] ?? 0;
        $instance->timedOut = $data['timed_out'] ?? false;
        $instance->total = $data['hits']['total']['value'] ?? ($data['hits']['total'] ?? 0);
        $instance->maxScore = $data['hits']['max_score'] ?? null;
        $instance->items = $instance->toEntities($entity, $data['hits']['hits'] ?? []);
        $instance->aggregations = $data['aggregations'] ?? [];

        return $instance;
    }


    /**
     * @param callable $callback
     * @return $this
     */
    public function each(callable $callback): static
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * @param int $prePage
     * @param int $currentPage
     * @return LengthAwarePaginator
     */
    public function toPaginator(int $prePage, int $currentPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator($this->items, $this->total, $prePage, $currentPage);
    }

    /**
     * @param EsEntityInterface $entity
     * @param $items
     * @return array
     */
    protected function toEntities(EsEntityInterface $entity, $items): array
    {
        $entities = [];
        foreach ($items as $item) {
            $score = $item['_score'] ?? 0;
            $source = $item['_source'] ?? [];
            $entities[] = $entity
                ->newInstance($source)
                ->setScore($score)
                ->setCriticalScore($entity->getCriticalScore());
        }

        return $entities;
    }

    /**
     * @param string $key
     * @return array
     */
    public function pluck(string $key): array
    {
        $result = [];
        foreach ($this->items as $item) {
            if (isset($item->{$key})) {
                $result[] = $item->{$key};
            }
        }

        return $result;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->items[$offset]);
        }
    }
}
