<?php
declare(strict_types=1);

namespace Golly\Elastic\Hydrate;

use Golly\Hydrate\Annotations\Source;
use Golly\Hydrate\Entity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Class ElasticEntity
 * @package Golly\Elastic\Hydrate
 */
class ElasticEntity extends Entity
{

    /**
     * 整个搜索请求花费多少毫秒
     *
     * @var int
     */
    public int $took = 0;

    /**
     * @Source(field="timed_out")
     * @var bool
     */
    public bool $timedOut = false;

    /**
     * @Source(field="hits.total")
     * @var int|array
     */
    public int|array $total = 0;

    /**
     * @Source(field="hits.max_score")
     * @var float|null
     */
    public ?float $maxScore;

    /**
     * @Source(field="hits.hits")
     * @var array
     */
    public array $source = [];

    /**
     * @var array
     */
    public array $aggregations = [];

    /**
     * @return array
     */
    public static function mapping(): array
    {
        return Reflection::mapping(new static());
    }

    /**
     * @param string $key
     * @return array
     */
    public function pluck(string $key = '_id'): array
    {
        return Arr::pluck($this->source, $key);
    }

    /**
     * @param int $prePage
     * @param int $currentPage
     * @param Collection|null $items
     * @return LengthAwarePaginator
     */
    public function paginate(int $prePage, int $currentPage, ?Collection $items = null): LengthAwarePaginator
    {
        $items = is_null($items) ? $this->source : $items;
        if (is_array($this->total)) {
            $this->total = Arr::get($this->total, 'value', 0);
        }

        return new LengthAwarePaginator(
            $items, $this->total, $prePage, $currentPage
        );
    }

}
