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
        $this->start();
    }

    protected function start() {
        Logger::instant('Starting app');
        $q = 'select `k`, `method` from `algos`';
        try {
            $this->supportedAlgos = SQL::getInstance()->qk($q, 'k', 'method');
            SQL::getInstance()->execRead('select * from `rainbow` limit 1');
        } catch(\Throwable $e) { // does not exists, prolly
            Logger::instant('App is not initialized yet, initializing');
            if($this->initialize() && !$this->isStarted) {
                $this->isStarted = true;
                $this->start();
            } else {
                Logger::instant('Error while registering supported algos');
                throw new Exception('Error while registering supported algos');
            }
        }
    }

    public function initialize(): bool {
        $r = true;

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
    primary key (`clear`, `algo`),
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
        $st = mb_ord('!'); // ! is the first printable ascii char, we'll keep it simple, stupid... but unicode
        $mn = 5;
        $ln = 5+mt_rand(1, 10);
        for($i = $mn; $i < $ln; $i++) {
            $s = $st+mt_rand(0, 93);
            $r .= mb_chr($s); // there are 95 printable characters in ascii / UTF-8
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
            $qa['a'.$i] = $ak;
            $qa['h'.$i] = $h;
            $entries[] = '(:clear, :a'.$i.', :h'.$i.')';
        }
        if(!empty($entries)) {
            SQL::getInstance()->execWrite('insert ignore into `rainbow` 
                (`clear`, `algo`, `value`)
                values '.implode(', ', $entries).'', $qa);
        }
    }

    public function searchHash(string $hash) {
        return SQL::getInstance()->qf('select * from `rainbow` where `value`=:h', ['h' => $hash,]);
    }

    public function getTotalCount():int {
        $r = SQL::getInstance()->qo('select count(*) as nb from `rainbow`', 'nb');
        return empty($r)? 0:intval($r);
    }
}
