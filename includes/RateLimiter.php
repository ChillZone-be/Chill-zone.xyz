<?php

class RateLimiter {
    private $redis;
    private $prefix = 'rate_limit:';
    private $maxAttempts;
    private $decayMinutes;
    
    public function __construct($maxAttempts = 5, $decayMinutes = 60) {
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }
    
    public function tooManyAttempts($key, $ip) {
        $key = $this->prefix . $key . ':' . $ip;
        $attempts = isset($_SESSION[$key]) ? $_SESSION[$key] : 0;
        
        if ($attempts >= $this->maxAttempts) {
            if (!isset($_SESSION[$key . '_timestamp'])) {
                return false;
            }
            
            $timestamp = $_SESSION[$key . '_timestamp'];
            if (time() - $timestamp >= $this->decayMinutes * 60) {
                $this->resetAttempts($key, $ip);
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
    public function hit($key, $ip) {
        $key = $this->prefix . $key . ':' . $ip;
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = 1;
            $_SESSION[$key . '_timestamp'] = time();
        } else {
            $_SESSION[$key]++;
        }
    }
    
    public function resetAttempts($key, $ip) {
        $key = $this->prefix . $key . ':' . $ip;
        unset($_SESSION[$key]);
        unset($_SESSION[$key . '_timestamp']);
    }
    
    public function retriesLeft($key, $ip) {
        $key = $this->prefix . $key . ':' . $ip;
        $attempts = isset($_SESSION[$key]) ? $_SESSION[$key] : 0;
        return $this->maxAttempts - $attempts;
    }
}
