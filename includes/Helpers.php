<?php

class Helpers {
    private static $config = null;
    
    /**
     * Lädt und cached die Konfiguration
     */
    public static function config($key = null) {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../config/app.php';
        }
        
        if ($key === null) {
            return self::$config;
        }
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return null;
            }
            $value = $value[$segment];
        }
        
        return $value;
    }
    
    /**
     * Sicheres Generieren von temporären Dateinamen
     */
    public static function generateTempFilename($extension) {
        $random = bin2hex(random_bytes(16));
        return sys_get_temp_dir() . '/upload_' . $random . '.' . $extension;
    }
    
    /**
     * Validiert eine E-Mail-Adresse
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Bereinigt einen String für die sichere Ausgabe
     */
    public static function sanitize($string) {
        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Formatiert Bytes in lesbare Größen
     */
    public static function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Prüft ob der Debug-Modus aktiv ist
     */
    public static function isDebug() {
        return self::config('app.debug') === true;
    }
    
    /**
     * Gibt eine Fehlermeldung aus
     */
    public static function error($message, $code = 400) {
        http_response_code($code);
        if (self::isDebug()) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
            echo json_encode([
                'error' => $message,
                'file' => $file,
                'line' => $line
            ]);
        } else {
            echo json_encode(['error' => $message]);
        }
        exit;
    }
    
    /**
     * Gibt eine Erfolgsmeldung aus
     */
    public static function success($message, $data = []) {
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}
