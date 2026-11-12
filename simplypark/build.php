<?php
/**
 * Generates a static index.html from index.php, for hosting without PHP.
 * Run:  php build.php
 */
ob_start();
require __DIR__ . '/index.php';
file_put_contents(__DIR__ . '/index.html', ob_get_clean());
echo "index.html written\n";
