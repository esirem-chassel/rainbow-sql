<?php
namespace App\Rainbow\lib;

use App\Rainbow\traits\Singleton;
use Override;

class Env {
    use Singleton;

    const PATH = '.env';

    #[Override]
    protected function __construct() {
        $fn = __DIR__.'/../../'.static::PATH;
        $envs = file($fn);
        foreach($envs as $line) {
            $line = trim($line);
            if(!empty($line) && !str_starts_with($line, '#')) {
                putenv($line);
            }
        }
    }

    public function has(string $key): bool {
        return array_key_exists($key, $_ENV) || (false !== getenv($key));
    }

    public function get(string $key, mixed $def = null): mixed {
        $r = $def;
        if(array_key_exists($key, $_ENV)) {
            $r = $_ENV[$key];
        } else if(false !== getenv($key)) {
            $r = getenv($key);
        }
        return $r;
    }
}
