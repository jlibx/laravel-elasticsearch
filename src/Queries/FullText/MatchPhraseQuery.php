<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\FullText;

/**
 * Class MatchPhraseQuery
 * @package Golly\Elastic\Queries\FullText
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html
 */
class MatchPhraseQuery extends MatchQuery
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'match_phrase';
    }
}
