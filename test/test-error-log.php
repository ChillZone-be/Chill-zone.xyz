<?php
require_once __DIR__ . '/../error-logger.php';

// Test verschiedener Fehlertypen
function testErrorLogging() {
    // Test Warning
    trigger_error("Test Warning", E_USER_WARNING);
    
    // Test Notice
    trigger_error("Test Notice", E_USER_NOTICE);
    
    // Test Error
    trigger_error("Test Error", E_USER_ERROR);
}

// Debug Log Test
debugLog("Test Debug Message", ["test" => true]);

// Führe Tests aus
testErrorLogging();
