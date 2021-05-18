<?php


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
    public $took = 0;

    /**
     * @Source(field="timed_out")
     * @var bool
     */
    public $timedOut = false;

    /**
     * @Source(field="hits.total.value")
     * @var int
     */
    public $total = 0;

    /**
     * @Source(field="hits.max_score")
     * @var
     */
    public $maxScore;

    /**
     * @Source(field="hits.hits")
     * @var array
     */
    public $source = [];

    /**
     * @var array
     */
    public $aggregations = [];

    /**
     * @return array
     */
    public static function mapping()
    {
        $self = new static();

        return $self->newReflection()->map($self);
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return Arr::pluck($this->source, '_id');
    }

    /**
     * @param $prePage
     * @param $currentPage
     * @param Collection|null $items
     * @return LengthAwarePaginator
     */
    public function paginate($prePage, $currentPage, Collection $items = null)
    {
        $items = is_null($items) ? $this->source : $items;
        // 自定义总数
        if (method_exists($this, 'customTotal')) {
            $this->total = $this->customTotal();
        }

        return new LengthAwarePaginator(
            $items, $this->total, $prePage, $currentPage
        );
    }

    /**
     * @return Reflection
     */
    protected function newReflection()
    {
        return new Reflection();
    }

}
