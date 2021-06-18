<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\TermLevel;

use Golly\Elastic\Queries\Query;

/**
 * Class ExistsQuery
 * @package Golly\Elastic\Queries\TermLevel
 */
class ExistsQuery extends Query
{

    /**
     * ExistsQuery constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'exists';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
<<<<<<< HEAD
        return [];
    }


=======
        return [$this->field];
    }

>>>>>>> 64a8b9acb1bddde04015f9df69ba7eff70bc795a
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
<<<<<<< HEAD
            $this->getType() => $this->field
=======
            $this->getType() => (string)$this->getTypeValue()
>>>>>>> 64a8b9acb1bddde04015f9df69ba7eff70bc795a
        ];
    }
}
