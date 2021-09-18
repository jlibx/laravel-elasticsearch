<?php
declare(strict_types=1);

namespace Kabunx\LaravelElasticsearch\Contracts;

interface EsEntityInterface
{
    public function getProperties(): array;

    public function getScore(): ?float;

    public function setScore(?float $score): static;

    public function getCriticalScore(): ?float;

    public function setCriticalScore(?float $criticalScore): void;

}
