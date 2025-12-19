<?php

namespace core;

use PDO;
use PDOException;

/**
 * Database class - PDO Connection
 */
class Database
{
    private static $instance = null;
    private $connection;
    private $statement;

    private function __construct()
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ':' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Database Connection Error: ' . $e->getMessage());
        }
    }

    /**
     * Singleton Pattern - Get Database Instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Prepare statement
     */
    public function prepare($sql)
    {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }

    /**
     * Bind parameter
     */
    public function bind($param, $value, $type = PDO::PARAM_STR)
    {
        $this->statement->bindValue($param, $value, $type);
        return $this;
    }

    /**
     * Bind multiple parameters
     */
    public function bindParams($params)
    {
        foreach ($params as $key => $value) {
            $type = PDO::PARAM_STR;
            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            }
            $this->bind($key, $value, $type);
        }
        return $this;
    }

    /**
     * Execute statement
     */
    public function execute()
    {
        try {
            return $this->statement->execute();
        } catch (PDOException $e) {
            die('Query Error: ' . $e->getMessage());
        }
    }

    /**
     * Get single row
     */
    public function single()
    {
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all rows
     */
    public function getAll()
    {
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get row count
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        return $this->connection->rollback();
    }
}
