<?php


namespace Golly\Elastic\Contracts;

/**
 * Interface EndpointInterface
 * @package Golly\Elastic\Contracts
 */
interface EndpointInterface
{

    /**
     * @param array $params
     * @return mixed
     */
    public function setParams(array $params);

    /**
     * @return array
     */
    public function normalize();

    /**
     * @return array
     */
    public function getContainers();

    /**
     * @param array $containers
     * @return void
     */
    public function setContainers(array $containers);

    /**
     * @param mixed $container
     * @return void
     */
    public function addContainer($container);

}
