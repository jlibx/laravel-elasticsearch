<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries;

use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\HasParams;
use stdClass;

/**
 * Class Query
 * @package Golly\Elastic\Queries
 */
abstract class Query implements QueryInterface
{
    use HasParams;

    /**
     * @var string
     */
    protected string $field;

    /**
     * @var mixed
     */
    protected mixed $value;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->getType() => $this->getTypeValue() ?: new stdClass()
        ];
    }
}
