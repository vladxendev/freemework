<?php
declare(strict_types=1);

namespace Freemework\DB;

use Freemework\DB\QueryBuilder\QueryBuilder;
use PDO;

class DataBase
{
    public PDO $connect;
    
    public function __construct(PDO $pdo)
    {
        $this->connect = $pdo;
    }
}
