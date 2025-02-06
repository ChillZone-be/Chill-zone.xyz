<?php
require_once __DIR__ . '/../error-logger.php';

// Versuche einen Test-Eintrag zu schreiben
writeToLog("Test Logging Entry " . date('Y-m-d H:i:s'));

// Absichtlich einen Fehler erzeugen
$undefined_variable;

// Einen Fehler auslösen
trigger_error("Test Error Message", E_USER_WARNING);

echo "Test completed. Check error.log file.";
