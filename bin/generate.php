<?php
require_once __DIR__ . '/../src/includer.php';

use App\Rainbow\lib\Rainbow;

if(!empty($argc) && !empty($argv)) {
    if($argc > 1) {
        Rainbow::getInstance()->generateFixed($argv[1]);
    } else {
        Rainbow::getInstance()->generateOne();
    }
} else { // should never happen
    http_response_code(400);
    exit;
}
