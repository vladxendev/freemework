<?php
declare(strict_types=1);

namespace Freemework\DB\QueryBuilder\Driver;

class PgSql
{
	public function connect()
	{
		$options = [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		];
		
		$Connect = new \PDO("pgsql:host={$PgsqlSettings['pgsql']['host']};port={$PgsqlSettings['pgsql']['port']};dbname={$PgsqlSettings['pgsql']['dbname']}", $PgsqlSettings['pgsql']['user'], $PgsqlSettings['pgsql']['password'], $options);
		//$Connect = pg_connect("host={$PgsqlSettings['pgsql']['host']} port={$PgsqlSettings['pgsql']['port']} dbname={$PgsqlSettings['pgsql']['dbname']} user={$PgsqlSettings['pgsql']['user']} password={$PgsqlSettings['pgsql']['password']}");
		
		if ($Connect === false) {
			return false;
		} else {
			return $Connect;
		}
	}
	
	public static function createDb()
	{
		echo 'PgSql::createDb DataBase';
	}
	
	public static function deleteDb()
	{
		echo 'PgSql::deleteDb DataBase';
	}
	
	public static function createTable()
	{
		echo 'PgSql::createTable DataBase';
	}
	
	public static function deleteTable()
	{
		echo 'PgSql::deleteTable DataBase';
	}
	
	public static function insert($table, $fields)
	{
		$pdo = self::connect();
		
		foreach ($fields as $column => $value) {
			if ($column !== array_key_last($fields)) {
				$columns[$column] = $column . ', ';
				$values[$column] = ':' . $column . ', ';
			} else {
				$columns[$column] = $column;
				$values[$column] = ':' . $column;
			}
			// Auto set data types for query values: $var = is_integer($data) ? PDO::PARAM_INT : PDO::PARAM_STR;
			$bindValue[$column] = ':' . $column;
		}
		
		$columns =  implode ('', $columns);
		$values = implode ('', $values);
		$sqlQuery = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ");";
		$stmt = $pdo->prepare($sqlQuery);
		
		foreach ($bindValue as $bindKey => $bindVal) {
			$stmt->bindValue($bindVal, $fields[$bindKey]);
		}
		return $stmt->execute();
	}
	
	public static function insertId($table, $fields)
	{
		$pdo = self::connect();
		
		foreach ($fields as $column => $value) {
			if ($column !== array_key_last($fields)) {
				$columns[$column] = $column . ', ';
				$values[$column] = ':' . $column . ', ';
			} else {
				$columns[$column] = $column;
				$values[$column] = ':' . $column;
			}
			$bindValue[$column] = ':' . $column;
		}
		
		$columns =  implode ('', $columns);
		$values = implode ('', $values);
		$sqlQuery = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ");";
		$stmt = $pdo->prepare($sqlQuery);
		
		foreach ($bindValue as $bindKey => $bindVal) {
			$stmt->bindValue($bindVal, $fields[$bindKey]);
		}
		$stmt->execute();
		return $pdo->lastInsertId();
	}
	
	public static function update($table, $fields, $params)
	{
		$pdo = self::connect();
		
		foreach ($fields as $column => $value) {
			if ($column !== array_key_last($fields)) {
				$sets[$column] = $column . ' = :val' . $column . ', ';
			} else {
				$sets[$column] = $column . ' = :val' . $column;
			}
			$bindValue[$column] = ':val' . $column;
		}
		unset($column);
		unset($value);
		
		foreach ($params as $column => $value) {
			if ($column !== array_key_last($params)) {
				$where[$column] = $column . ' = :par' . $column . ' AND ';
			} else {
				$where[$column] = $column . ' = :par' . $column;
			}
			$bindParam[$column] = ':par' . $column;
		}
		
		$columns =  implode ('', $sets);
		$updateBy = implode ('', $where);
		$sqlQuery = 'UPDATE ' . $table . ' SET ' . $columns . ' WHERE ' . $updateBy . ';';
		$stmt = $pdo->prepare($sqlQuery);
		
		foreach ($bindValue as $bindKey => $bindVal) {
			$stmt->bindValue($bindVal, $fields[$bindKey]);
		}
		
		foreach ($bindParam as $PbindKey => $PbindVal) {
			$stmt->bindParam($PbindVal, $params[$PbindKey]);
		}
		
		return $stmt->execute();
	}
	
	public static function getRow($table, $fields, $params)
	{
		$pdo = self::connect();
		
		foreach ($params as $column => $value) {
			if ($column !== array_key_last($params)) {
				$where[$column] = $column . ' = :' . $column . ' AND ';
			} else {
				$where[$column] = $column . ' = :' . $column;
			}
			$bindParam[$column] = ':' . $column;
		}
		
		$columns =  implode ('', $where);
		$sqlQuery = 'SELECT ' . $fields . ' FROM ' . $table . ' WHERE ' . $columns.';';
		$stmt = $pdo->prepare($sqlQuery);
		
		foreach ($bindParam as $bindKey => $bindVal) {
			$stmt->bindParam($bindVal, $params[$bindKey]);
		}
		$stmt->execute();
		
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
		if (isset($rows)) {
			return $rows;
		} else {
			return false;
		}
	}
	
	public static function getRows($table, $fields, $params, $order = '1 ASC', $limit = 'ALL', $offset = 0)
	{
		$pdo = self::connect();
		
		foreach ($params as $column => $value) {
			if ($column !== array_key_last($params)) {
				$where[$column] = $column . ' = :' . $column . ' AND ';
			} else {
				$where[$column] = $column . ' = :' . $column;
			}
			$bindParam[$column] = ':' . $column;
		}
		
		$columns =  implode ('', $where);
		$sqlQuery = 'SELECT ' . $fields . ' FROM ' . $table . ' WHERE ' . $columns . ' ORDER BY ' . $order . ' OFFSET ' . $offset . ' LIMIT ' . $limit . ';';
		$stmt = $pdo->prepare($sqlQuery);
		
		foreach ($bindParam as $bindKey => $bindVal) {
			$stmt->bindParam($bindVal, $params[$bindKey]);
		}
		$stmt->execute();
		
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
		if (isset($rows)) {
			return $rows;
		} else {
			return false;
		}
	}
	
	public static function getAllRows($table, $order = '1 ASC', $limit = 'ALL', $offset = 0)
	{
		$pdo = self::connect();
		
		$sqlQuery = 'SELECT * FROM ' . $table . ' ORDER BY ' . $order . ' OFFSET ' . $offset . ' LIMIT ' . $limit . ';';
		$stmt = $pdo->prepare($sqlQuery);
		$stmt->execute();
		
        while ($row = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
		if (isset($rows)) {
			return $rows;
		} else {
			return false;
		}
	}
	
	public static function deleteRow($table, $params)
	{
		$pdo = self::connect();
		
		foreach ($params as $column => $value) {
			if ($column !== array_key_last($params)) {
				$where[$column] = $column . ' = :' . $column . ' AND ';
			} else {
				$where[$column] = $column . ' = :' . $column;
			}
			$bindParam[$column] = ':' . $column;
		}
		
		$columns =  implode ('', $where);
		$sqlQuery = 'DELETE FROM ' . $table . ' WHERE ' . $columns . ';';
		$stmt = $pdo->prepare($sqlQuery);
		
		foreach ($bindParam as $bindKey => $bindVal) {
			$stmt->bindParam($bindVal, $params[$bindKey]);
		}
		
		$stmt->execute();
		return $stmt->rowCount();
	}
	
	public static function deleteRows($table)
	{
		$pdo = self::connect();
		
		$sqlQuery = 'DELETE FROM ' . $table . ';';
		$stmt = $pdo->prepare($sqlQuery);
		
		$stmt->execute();
		return $stmt->rowCount();
	}
	
	public static function freeQuery($sql)
	{
		$pdo = self::connect();
		return $pdo->query($sql, \PDO::FETCH_ASSOC);
	}
	
	public static function countRows($table)
	{
		$pdo = self::connect();
		$stmt = $pdo->query("SELECT COUNT(*) FROM {$table};");
		return $stmt->fetch(\PDO::FETCH_NUM);
	}
	
	public static function freeExec()
	{
		echo 'PgSql::freeExec DataBase';
	}
}
