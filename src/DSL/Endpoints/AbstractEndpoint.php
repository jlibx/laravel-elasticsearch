<?php


namespace Golly\Elastic\DSL\Endpoints;


use Golly\Elastic\Contracts\EndpointInterface;
use Golly\Elastic\DSL\HasParams;

/**
 * Class AbstractEndpoint
 * @package Golly\Elastic\DSL\Endpoints
 */
abstract class AbstractEndpoint implements EndpointInterface
{
    use HasParams;

    /**
     * @var array
     */
    protected $containers = [];

    /**
     * @return array
     */
    public function getContainers(): array
    {
        return $this->containers;
    }

    /**
     * @param array $containers
     * @return void
     */
    public function setContainers(array $containers)
    {
        $this->containers = $containers;
    }

    /**
     * @param mixed $container
     * @return void
     */
    public function addContainer($container)
    {
        $this->containers[] = $container;
    }

}
