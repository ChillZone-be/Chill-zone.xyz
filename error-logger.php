<?php
// Basis-Konfiguration
$logDir = __DIR__ . '/logs';
$logFile = $logDir . '/error.log';

// Verzeichnis erstellen und Berechtigungen setzen
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
    chmod($logDir, 0777);
}

// Log-Datei erstellen und Berechtigungen setzen
if (!file_exists($logFile)) {
    touch($logFile);
    chmod($logFile, 0666);
}

// Fehlerbehandlung aktivieren
ini_set('log_errors', 1);
ini_set('error_log', $logFile);
error_reporting(E_ALL);

// Einfache Testfunktion
function writeToLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    
    // Direktes Schreiben in die Log-Datei
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    
    // Auch mit error_log versuchen
    error_log($message);
    
    return true;
}
