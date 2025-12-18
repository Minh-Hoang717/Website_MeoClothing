<?php
/**
 * Base Model
 * Model cơ sở cho tất cả các model khác
 */

abstract class BaseModel {
    
    protected $conn;
    protected $table;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all records with pagination
     */
    public function getAll($page = 1, $pageSize = 20, $orderBy = null) {
        $offset = ($page - 1) * $pageSize;
        
        $orderClause = $orderBy ? "ORDER BY $orderBy" : "";
        
        $query = "SELECT * FROM {$this->table} $orderClause LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get total count
     */
    public function getCount($where = null, $params = []) {
        $whereClause = $where ? "WHERE $where" : "";
        
        $query = "SELECT COUNT(*) as total FROM {$this->table} $whereClause";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch();
        
        return (int) $row['total'];
    }
    
    /**
     * Get by ID
     */
    public function getById($id, $idField = null) {
        $field = $idField ?? $this->getPrimaryKey();
        
        $query = "SELECT * FROM {$this->table} WHERE $field = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Create record
     */
    public function create($data) {
        $fields = array_keys($data);
        $fieldsList = implode(', ', $fields);
        $valuesList = ':' . implode(', :', $fields);
        
        $query = "INSERT INTO {$this->table} ($fieldsList) VALUES ($valuesList)";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update record
     */
    public function update($id, $data, $idField = null) {
        $field = $idField ?? $this->getPrimaryKey();
        
        $sets = [];
        foreach (array_keys($data) as $key) {
            $sets[] = "$key = :$key";
        }
        $setClause = implode(', ', $sets);
        
        $query = "UPDATE {$this->table} SET $setClause WHERE $field = :id";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete record
     */
    public function delete($id, $idField = null) {
        $field = $idField ?? $this->getPrimaryKey();
        
        $query = "DELETE FROM {$this->table} WHERE $field = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get primary key field name
     */
    protected function getPrimaryKey() {
        // Override in child classes if needed
        return substr($this->table, 0, -1) . '_id';
    }
    
    /**
     * Execute custom query
     */
    protected function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $stmt;
    }
}
?>
