<?php
require_once __DIR__ . '/../src/includer.php';

use App\Rainbow\lib\Rainbow;

if(!empty($argc) && !empty($argv)) {
    $nb = 100;
    if($argc > 1) {
        $nb = max(1, intval($argv[1]));
    }
    Rainbow::getInstance()->massGenerate($nb);
} else { // should never happen
    http_response_code(400);
    exit;
}
