<?php
header_remove("X-Powered-By");
$start = (double)microtime(TRUE);
$GLOBALS['output'] = function ($line) {
//    var_dump($line);
};



(require __DIR__ . '/../app/Application.php')->run();
//var_dump("index.php",((double)microtime(TRUE) - $start) * 1000);