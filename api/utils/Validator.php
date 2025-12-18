<?php
/**
 * Input Validation Utility
 */

class Validator {
    
    private $errors = [];
    
    /**
     * Validate required field
     */
    public function required($field, $value, $fieldName = null) {
        if (empty($value) && $value !== '0' && $value !== 0) {
            $name = $fieldName ?? $field;
            $this->errors[$field] = "$name is required";
            return false;
        }
        return true;
    }
    
    /**
     * Validate email
     */
    public function email($field, $value, $fieldName = null) {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $name = $fieldName ?? $field;
            $this->errors[$field] = "$name must be a valid email";
            return false;
        }
        return true;
    }
    
    /**
     * Validate minimum length
     */
    public function minLength($field, $value, $min, $fieldName = null) {
        if (!empty($value) && strlen($value) < $min) {
            $name = $fieldName ?? $field;
            $this->errors[$field] = "$name must be at least $min characters";
            return false;
        }
        return true;
    }
    
    /**
     * Validate maximum length
     */
    public function maxLength($field, $value, $max, $fieldName = null) {
        if (!empty($value) && strlen($value) > $max) {
            $name = $fieldName ?? $field;
            $this->errors[$field] = "$name must not exceed $max characters";
            return false;
        }
        return true;
    }
    
    /**
     * Validate numeric
     */
    public function numeric($field, $value, $fieldName = null) {
        if (!empty($value) && !is_numeric($value)) {
            $name = $fieldName ?? $field;
            $this->errors[$field] = "$name must be a number";
            return false;
        }
        return true;
    }
    
    /**
     * Validate decimal
     */
    public function decimal($field, $value, $decimals = 2, $fieldName = null) {
        if (!empty($value) && !preg_match('/^\d+(\.\d{1,' . $decimals . '})?$/', $value)) {
            $name = $fieldName ?? $field;
            $this->errors[$field] = "$name must be a valid decimal number";
            return false;
        }
        return true;
    }
    
    /**
     * Validate date
     */
    public function date($field, $value, $format = 'Y-m-d', $fieldName = null) {
        if (!empty($value)) {
            $d = DateTime::createFromFormat($format, $value);
            if (!$d || $d->format($format) !== $value) {
                $name = $fieldName ?? $field;
                $this->errors[$field] = "$name must be a valid date ($format)";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate in array
     */
    public function in($field, $value, $allowed, $fieldName = null) {
        if (!empty($value) && !in_array($value, $allowed)) {
            $name = $fieldName ?? $field;
            $allowedStr = implode(', ', $allowed);
            $this->errors[$field] = "$name must be one of: $allowedStr";
            return false;
        }
        return true;
    }
    
    /**
     * Get validation errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Check if validation failed
     */
    public function fails() {
        return !empty($this->errors);
    }
    
    /**
     * Clear errors
     */
    public function clearErrors() {
        $this->errors = [];
    }
}
?>
