<?php

namespace core;

/**
 * Base Model Class
 */
abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey; // Primary key column name

    public function __construct()
    {
        $this->db = Database::getInstance();
        // Default primary key: singular table name + _id
        if (!$this->primaryKey) {
            $this->primaryKey = rtrim($this->table, 's') . '_id';
        }
    }

    /**
     * Get all records
     */
    public function getAll($limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->db->prepare($sql)->getAll();
    }

    /**
     * Get record by ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->prepare($sql)
            ->bind(':id', $id, \PDO::PARAM_INT)
            ->single();
    }

    /**
     * Get record by column
     */
    public function getByColumn($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        return $this->db->prepare($sql)
            ->bind(':value', $value)
            ->single();
    }

    /**
     * Get all records by column
     */
    public function getAllByColumn($column, $value, $limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->db->prepare($sql)
            ->bind(':value', $value)
            ->getAll();
    }

    /**
     * Insert record
     */
    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ':' . $key, array_keys($data)));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $this->db->prepare($sql)->bindParams($data)->execute();
        
        return $this->db->lastInsertId();
    }

    /**
     * Update record
     */
    public function update($id, $data)
    {
        $sets = implode(', ', array_map(fn($key) => $key . ' = :' . $key, array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = :id";
        
        $data['id'] = $id;
        
        return $this->db->prepare($sql)->bindParams($data)->execute();
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->prepare($sql)
            ->bind(':id', $id, \PDO::PARAM_INT)
            ->execute();
    }

    /**
     * Count records
     */
    public function count($column = null, $value = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if ($column && $value !== null) {
            $sql .= " WHERE {$column} = :value";
            return $this->db->prepare($sql)
                ->bind(':value', $value)
                ->single()['count'];
        }
        
        return $this->db->prepare($sql)->single()['count'];
    }
}
