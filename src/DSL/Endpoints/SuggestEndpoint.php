<?php


namespace Golly\Elastic\DSL\Endpoints;


use Golly\Elastic\Contracts\SuggestInterface;

/**
 * Class SuggestEndpoint
 * @package Golly\Elastic\DSL\Endpoints
 */
class SuggestEndpoint extends AbstractEndpoint
{

    const NAME = 'suggest';

    /**
     * @return array
     */
    public function normalize()
    {
        $output = [];
        /** @var SuggestInterface $suggest */
        foreach ($this->containers as $suggest) {
            $output = array_merge($output, $suggest->toArray());
        }

        return $output;
    }
}
