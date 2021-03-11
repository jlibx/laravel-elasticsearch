<?php


namespace Golly\Elastic\DSL;


/**
 * Trait HasName
 * @package Golly\Elastic\DSL
 */
trait HasName
{

    /**
     * @var string
     */
    protected $name;


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

}
