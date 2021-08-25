<?php
declare(strict_types=1);

namespace Freemework\DB\QueryBuilder\Query;

class Insert
{
    private const SQL_QUERY = <<<SQL_PATTERN
Query
SQL_PATTERN;

    public function getSql()
    {
        return self::SQL_QUERY;
    }
}
