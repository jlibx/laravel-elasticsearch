<?php


namespace Golly\Elastic\Queries\FullText;


/**
 * Class MatchBoolPrefixQuery
 * @package Golly\Elastic\Queries\FullText
 */
class MatchBoolPrefixQuery extends MatchQuery
{

    /**
     * @return string
     */
    public function getType()
    {
        return 'match_bool_prefix';
    }
}
