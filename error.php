<?php
function shutdown_handler() {
    $error = error_get_last();
    if ($error !== NULL && $error['type'] === E_ERROR) {
        header("HTTP/1.1 500 Internal Server Error");
        include '500.html';
        exit();
    }
}
register_shutdown_function('shutdown_handler');

// Absichtlich einen fatalen Fehler erzeugen
class Test {
    private function privateMethod() {}
}

$test = new Test();
$test->privateMethod(); // Dies wird einen fatalen Fehler erzeugen
?>
