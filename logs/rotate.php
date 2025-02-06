<?php
// Log Rotation Script
function rotateLog($logFile, $maxSize = 5242880, $keepFiles = 5) { // 5MB default
    if (file_exists($logFile) && filesize($logFile) > $maxSize) {
        for ($i = $keepFiles - 1; $i > 0; $i--) {
            $oldFile = $logFile . '.' . $i;
            $newFile = $logFile . '.' . ($i + 1);
            if (file_exists($oldFile)) {
                rename($oldFile, $newFile);
            }
        }
        rename($logFile, $logFile . '.1');
        file_put_contents($logFile, '');
    }
}

// Rotate all log files
$logFiles = [
    __DIR__ . '/error.log',
    __DIR__ . '/access.log',
    __DIR__ . '/mail.log',
    __DIR__ . '/security.log',
    __DIR__ . '/debug.log'
];

foreach ($logFiles as $logFile) {
    rotateLog($logFile);
}
