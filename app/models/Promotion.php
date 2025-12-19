<?php

namespace models;

use core\Model;

/**
 * Promotion Model
 */
class Promotion extends Model
{
    protected $table = 'promotions';

    /**
     * Get active promotion by code
     */
    public function getActiveByCode($code)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE code = :code 
                AND start_date <= NOW() 
                AND end_date >= NOW()";
        
        return $this->db->prepare($sql)
            ->bind(':code', $code)
            ->single();
    }

    /**
     * Get all active promotions
     */
    public function getActive()
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE start_date <= NOW() 
                AND end_date >= NOW()
                ORDER BY discount_value DESC";
        
        return $this->db->prepare($sql)->getAll();
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($promotion, $amount)
    {
        if ($promotion['discount_type'] === 'percentage') {
            return ($amount * $promotion['discount_value']) / 100;
        }
        
        return $promotion['discount_value'];
    }
}
