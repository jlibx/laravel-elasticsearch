<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\FullText;

/**
 * Class MatchPhrasePrefixQuery
 * @package Golly\Elastic\Queries\FullText
 */
class MatchPhrasePrefixQuery extends MatchQuery
{

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'match_phrase_prefix';
    }

}
