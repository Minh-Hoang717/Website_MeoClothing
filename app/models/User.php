<?php

namespace models;

use core\Model;

/**
 * User Model
 */
class User extends Model
{
    protected $table = 'users';

    /**
     * Get user by username
     */
    public function getByUsername($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username";
        return $this->db->prepare($sql)
            ->bind(':username', $username)
            ->single();
    }

    /**
     * Get user by email
     */
    public function getByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        return $this->db->prepare($sql)
            ->bind(':email', $email)
            ->single();
    }

    /**
     * Authenticate user
     */
    public function authenticate($username, $password)
    {
        $user = $this->getByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    /**
     * Get staff users
     */
    public function getStaff()
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'staff' OR role = 'admin'";
        return $this->db->prepare($sql)->getAll();
    }

    /**
     * Create user
     */
    public function createUser($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->insert($data);
    }
}
