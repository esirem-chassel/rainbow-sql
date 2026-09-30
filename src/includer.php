<?php
namespace App\Rainbow;

spl_autoload_register(function($cb) {
    if((null !== $cb) && is_string($cb)) {
        $cb = preg_replace('`[^a-zA-Z0-9_\\\\]`u', '', trim($cb));
        $rns = __NAMESPACE__;
        if(str_starts_with($cb, $rns)) { // are we in the root NS
            $nsc = '\\';
            $x = explode($nsc, $cb);
            $lx = count(explode($nsc, $rns));
            for($i = 0; $i < $lx; $i++) { array_shift($x); }
            $fn = __DIR__.'/'.implode('/', $x).'.php';
            if(file_exists($fn) && is_file($fn)) {
                require_once $fn;
            }
        }
    }
});


