<?php


namespace Golly\Elastic\DSL\Endpoints;


use Golly\Elastic\Contracts\InnerHitInterface;

/**
 * Class InnerHitEndpoint
 * @package Golly\Elastic\DSL\Endpoints
 */
class InnerHitEndpoint extends AbstractEndpoint
{

    const NAME = 'inner_hits';

    /**
     * @return array
     */
    public function normalize()
    {
        $output = [];
        /**
         * @var InnerHitInterface $innerHit
         */
        foreach ($this->containers as $innerHit) {
            $output[$innerHit->getName()] = $innerHit->toArray();
        }

        return $output;
    }
}
