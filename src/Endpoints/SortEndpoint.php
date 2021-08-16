<?php

namespace Golly\Elastic\Endpoints;

use Golly\Elastic\Sorts\FieldSort;
use Golly\Elastic\Sorts\ScriptSort;

/**
 * Class SortEndpoint
 * @package Golly\Elastic\Endpoints
 */
class SortEndpoint extends Endpoint
{

    /**
     * @var ScriptSort|null
     */
    protected $script = null;

    /**
     * @return string
     */
    public function getName()
    {
        return 'sort';
    }

    /**
     * @param string $field
     * @param string $direction
     */
    public function addFieldSort(string $field, $direction = 'asc')
    {
        $this->addContainer(
            new FieldSort($field, $direction), $field
        );
    }

    /**
     * @return array
     */
    public function normalize()
    {
        if ($this->script) {
            return $this->script->toArray();
        } else {
            return parent::normalize();
        }
    }

    /**
     *
     */
    public function withRandomSort()
    {
        $this->script = (new ScriptSort())->random();
    }

}
