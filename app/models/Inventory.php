<?php

namespace models;

use core\Model;

/**
 * Inventory Model
 */
class Inventory extends Model
{
    protected $table = 'inventory';

    /**
     * Get inventory by variant ID
     */
    public function getByVariantId($variantId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE variant_id = :variant_id";
        return $this->db->prepare($sql)
            ->bind(':variant_id', $variantId, \PDO::PARAM_INT)
            ->single();
    }

    /**
     * Update inventory quantity
     */
    public function updateQuantity($variantId, $quantity)
    {
        $sql = "UPDATE {$this->table} SET quantity = :quantity WHERE variant_id = :variant_id";
        return $this->db->prepare($sql)
            ->bind(':quantity', $quantity, \PDO::PARAM_INT)
            ->bind(':variant_id', $variantId, \PDO::PARAM_INT)
            ->execute();
    }

    /**
     * Decrease quantity
     */
    public function decreaseQuantity($variantId, $amount)
    {
        $sql = "UPDATE {$this->table} SET quantity = quantity - :amount WHERE variant_id = :variant_id AND quantity >= :amount";
        return $this->db->prepare($sql)
            ->bind(':amount', $amount, \PDO::PARAM_INT)
            ->bind(':variant_id', $variantId, \PDO::PARAM_INT)
            ->execute();
    }

    /**
     * Increase quantity
     */
    public function increaseQuantity($variantId, $amount)
    {
        $sql = "UPDATE {$this->table} SET quantity = quantity + :amount WHERE variant_id = :variant_id";
        return $this->db->prepare($sql)
            ->bind(':amount', $amount, \PDO::PARAM_INT)
            ->bind(':variant_id', $variantId, \PDO::PARAM_INT)
            ->execute();
    }

    /**
     * Check if product is in stock
     */
    public function isInStock($variantId, $quantity = 1)
    {
        $sql = "SELECT quantity FROM {$this->table} WHERE variant_id = :variant_id AND quantity >= :quantity";
        return $this->db->prepare($sql)
            ->bind(':variant_id', $variantId, \PDO::PARAM_INT)
            ->bind(':quantity', $quantity, \PDO::PARAM_INT)
            ->single() !== false;
    }
}
