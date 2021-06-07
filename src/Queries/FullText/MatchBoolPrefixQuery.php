<?php
declare(strict_types=1);

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
    public function getType(): string
    {
        return 'match_bool_prefix';
    }
}
