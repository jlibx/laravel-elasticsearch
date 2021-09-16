<?php
declare(strict_types=1);

namespace Kabunx\Elastic\Contracts;

interface EsEntityInterface
{
    public function getProperties(): array;

    public function getScore(): ?float;

    public function setScore(?float $score): static;

}
