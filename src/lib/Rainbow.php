<?php
namespace App\Rainbow\lib;

use App\Rainbow\traits\Singleton;
use Exception;
use Override;

class Rainbow {
    use Singleton;

    protected array $supportedAlgos = [];
    protected bool $isStarted = false;

    #[Override]
    protected function __construct() {
        
    }

    protected function start() {
        $q = 'select `k`, `method` from `algos`';
        try {
            $this->supportedAlgos = SQL::getInstance()->qk($q, 'k', 'method');
        } catch(\Throwable $e) { // does not exists, prolly
            if($this->initialize() && !$this->isStarted) {
                $this->isStarted = true;
                $this->start();
            } else {
                throw new Exception('Error while registering supported algo');
            }
        }
    }

    public function initialize(): bool {
        $returns = true;

        $q = <<<'EOT'
create table if not exists `algos` (
    `k` varchar(20) not null primary key,
    `name` varchar(200) not null,
    `method` varchar(50) not null
)
EOT;
        $r = $r && SQL::getInstance()->execWriteAndForget($q);

        $q = <<<'EOT'
create table if not exists `rainbow` (
    `clear` varchar(50) not null,
    `algo` varchar(20) not null,
    `value` text not null,
    primary key (`clear`, `algo`)
    constraint `fk_rainbow_algo`
    foreign key (`algo`)
    references `algos`(`k`)
    on update cascade
    on delete restrict
)
EOT;
        $r = $r && SQL::getInstance()->execWriteAndForget($q);

        // fill supported algos
        $qa = [];
        $i = 0;
        foreach(hash_algos() as $algo) {
            $qa['a'.(++$i)] = $algo;
        }
        $q = "insert ignore into `algos` values".implode(', ', array_map(function($l){
            return '(:'.$l.', :'.$l.', :'.$l.')';
        }, array_keys($qa)));
        $r = $r && SQL::getInstance()->execWriteAndForget($q, $qa);
        return $r;
    }

    public function getSupportedAlgos(): array {
        return $this->supportedAlgos;
    }

    protected function rdm() {
        $r = '';
        $st = ord('!'); // ! is the first printable ascii char, we'll keep it simple, stupid
        for($i = 5; $i < 5+mt_rand(1, 10); $i++) {
            $r += chr($st+94); // there are 95 printable characters in ascii
        }
        return $r;
    }

    public function generateOne() {
        $rdm = $this->rdm();
        $qa = [
            'clear' => $rdm,
        ];
        $entries = [];
        $i = 0;
        foreach($this->getSupportedAlgos() as $ak => $am) {
            $i++;
            $h = hash($am, $rdm);
            $qa['a'.$i] = $am;
            $qa['h'.$i] = $h;
            $entries[] = '(:clear, :a'.$i.', :h'.$i.')';
        }
        if(!empty($entries)) {
            SQL::getInstance()->execWrite('insert ignore into `rainbow` 
                (`clear`, `algo`, `value`)
                values '.implode(', ', $entries).'', $qa);
        }
    }
}
