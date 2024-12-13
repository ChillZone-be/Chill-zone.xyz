<?php

class Logger {
    private $logFile;
    
    public function __construct($logFile) {
        $this->logFile = $logFile;
        
        // Erstelle Log-Verzeichnis falls es nicht existiert
        $logDir = dirname($logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    public function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
    
    public function error($message) {
        $this->log($message, 'ERROR');
    }
    
    public function info($message) {
        $this->log($message, 'INFO');
    }
    
    public function success($message) {
        $this->log($message, 'SUCCESS');
    }
}
