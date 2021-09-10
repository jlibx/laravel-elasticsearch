<?php
declare(strict_types=1);

namespace Kabunx\Elastic\Contracts;

interface SearchableInterface
{
    public function newEsEntity(): EsEntityInterface;

    /**
     * @return string
     */
    public function getEsIndex(): string;

    public function getEsProperties(): array;

    public function getEsIdName(): string;

    public function getEsId(): mixed;

    public function getEsPerPage(): int;

    public function getEsSoftDeletedColumn(): string;

    public function getEsSoftDeletedValue(): bool;

    public function useSoftDelete(): bool;

    /**
     * 忽略索引
     *
     * @return bool
     */
    public function ignoreSearchable(): bool;

    public function searchable(): void;

    public function toEsArray(): array;

    public function ifSoftDeletedAddMetadata(): void;

}
