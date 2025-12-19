<?php

namespace models;

use core\Model;

/**
 * ProductVariant Model
 */
class ProductVariant extends Model
{
    protected $table = 'productvariants';

    /**
     * Get variants by product ID
     */
    public function getByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = :product_id";
        return $this->db->prepare($sql)
            ->bind(':product_id', $productId, \PDO::PARAM_INT)
            ->getAll();
    }

    /**
     * Get variant with inventory
     */
    public function getVariantWithInventory($variantId)
    {
        $sql = "SELECT pv.*, i.quantity, p.name as product_name, p.image_path 
                FROM {$this->table} pv
                LEFT JOIN inventory i ON pv.variant_id = i.variant_id
                LEFT JOIN products p ON pv.product_id = p.product_id
                WHERE pv.variant_id = :variant_id";
        
        return $this->db->prepare($sql)
            ->bind(':variant_id', $variantId, \PDO::PARAM_INT)
            ->single();
    }

    /**
     * Get all variants with inventory for a product
     */
    public function getVariantsWithInventory($productId)
    {
        $sql = "SELECT pv.*, i.quantity 
                FROM {$this->table} pv
                LEFT JOIN inventory i ON pv.variant_id = i.variant_id
                WHERE pv.product_id = :product_id";
        
        return $this->db->prepare($sql)
            ->bind(':product_id', $productId, \PDO::PARAM_INT)
            ->getAll();
    }

    /**
     * Get variant by product, size, and color
     */
    public function getByProductSizeColor($productId, $size, $color)
    {
        $sql = "SELECT pv.*, i.quantity 
                FROM {$this->table} pv
                LEFT JOIN inventory i ON pv.variant_id = i.variant_id
                WHERE pv.product_id = :product_id AND pv.size = :size AND pv.color = :color";
        
        return $this->db->prepare($sql)
            ->bind(':product_id', $productId, \PDO::PARAM_INT)
            ->bind(':size', $size)
            ->bind(':color', $color)
            ->single();
    }
}
