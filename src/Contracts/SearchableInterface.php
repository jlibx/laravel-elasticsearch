<?php
declare(strict_types=1);

namespace Kabunx\LaravelElasticsearch\Contracts;

use Kabunx\LaravelElasticsearch\EsBuilder;

interface SearchableInterface
{
    public function newEsEntity(): EsEntityInterface;

    public function newEsBuilder(): EsBuilder;

    /**
     * @return string
     */
    public function getEsIndex(): string;

    public function getEsProperties(): array;

    public function getEsIdName(): string;

    public function getEsId(): mixed;

    public function getEsPerPage(): int;

    public function isUseSoftDeletes(): bool;

    public function getEsSoftDeletedColumn(): string;

    /**
     * 不确定数据结构的设计
     * 可能为：on、yes、1、true
     */
    public function getEsSoftDeletedValue(): mixed;

    public function getEsNotSoftDeletedValue(): mixed;

    /**
     * 忽略索引
     *
     * @return bool
     */
    public function ignoreSearchable(): bool;

    public function searchable(): void;

    public function toEsArray(): array;

    public function addMetadataIfSoftDeleted(): void;

}
