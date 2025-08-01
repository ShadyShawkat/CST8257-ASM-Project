<?php
// globals.php
// Contains all the global constants to easily find and use them.

// For use within php. Define base path for includes to avoid issues with different levels
define('BASE_PATH', __DIR__);
define('BASE_FOLDER', basename(dirname(__DIR__)));

// Constant can be used for html srcs or hrefs
$scriptNameDir = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', ($scriptNameDir === '/' || $scriptNameDir === '\\') ? '/' : rtrim($scriptNameDir, '/\\') . '/');

define('UPLOADS_FOLDER', BASE_PATH . DIRECTORY_SEPARATOR . 'uploads');