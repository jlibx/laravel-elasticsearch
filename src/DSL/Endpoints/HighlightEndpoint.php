<?php


namespace Golly\Elastic\DSL\Endpoints;


use Golly\Elastic\Contracts\HighlightInterface;

/**
 * Class HighlightEndpoint
 * @package Golly\Elastic\DSL\Endpoints
 */
class HighlightEndpoint extends AbstractEndpoint
{

    const NAME = 'highlight';

    /**
     * @var HighlightInterface
     */
    protected $highlight;

    /**
     * @return array|null
     */
    public function normalize()
    {
        if ($this->highlight) {
            return $this->highlight->toArray();
        }

        return null;
    }

    /**
     * @return array
     */
    public function getContainers(): array
    {
        return [$this->highlight];
    }

    /**
     * @param mixed $container
     * @return void
     */
    public function addContainer($container)
    {
        $this->highlight = $container;
    }

}
