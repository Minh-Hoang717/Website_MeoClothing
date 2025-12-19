<?php

namespace models;

use core\Model;

/**
 * Product Model
 */
class Product extends Model
{
    protected $table = 'products';

    /**
     * Get products with category info
     */
    public function getProductsWithCategory($limit = null, $offset = 0)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->db->prepare($sql)->getAll();
    }

    /**
     * Get product by ID with category
     */
    public function getProductWithCategory($id)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.product_id = :id";
        
        return $this->db->prepare($sql)
            ->bind(':id', $id, \PDO::PARAM_INT)
            ->single();
    }

    /**
     * Get products by category
     */
    public function getByCategory($categoryId, $limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} WHERE category_id = :category_id";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->db->prepare($sql)
            ->bind(':category_id', $categoryId, \PDO::PARAM_INT)
            ->getAll();
    }

    /**
     * Search products
     */
    public function search($keyword, $limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE name LIKE :keyword OR description LIKE :keyword";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->db->prepare($sql)
            ->bind(':keyword', '%' . $keyword . '%')
            ->getAll();
    }
}
