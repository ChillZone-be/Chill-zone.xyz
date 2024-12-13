<?php
// Aktiviere Fehlerberichterstattung
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');
error_reporting(E_ALL);

// Versuche in die Log-Datei zu schreiben
$logDir = __DIR__ . '/logs';
$logFile = $logDir . '/error.log';

// Debugging-Ausgabe
echo "Log Directory: " . $logDir . "<br>";
echo "Log File: " . $logFile . "<br>";
echo "Directory exists: " . (file_exists($logDir) ? 'Yes' : 'No') . "<br>";
echo "File exists: " . (file_exists($logFile) ? 'Yes' : 'No') . "<br>";
echo "Directory writable: " . (is_writable($logDir) ? 'Yes' : 'No') . "<br>";
echo "File writable: " . (file_exists($logFile) && is_writable($logFile) ? 'Yes' : 'No') . "<br>";

// Versuche direkt in die Datei zu schreiben
$testMessage = date('Y-m-d H:i:s') . " - Test message\n";
$writeResult = file_put_contents($logFile, $testMessage, FILE_APPEND);
echo "Write result: " . ($writeResult !== false ? 'Success' : 'Failed') . "<br>";

// Erzeuge einen Fehler
$undefined_variable;
trigger_error("This is a test error", E_USER_WARNING);

// Versuche error_log direkt
error_log("Direct error_log test");

phpinfo();
