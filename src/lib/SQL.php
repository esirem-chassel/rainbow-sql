<?php
namespace App\Rainbow\lib;

use App\Rainbow\traits\Singleton;
use Override;
use PDO;

class SQL {
    use Singleton;

    protected ?\PDO $sqlWriter = null;
    protected ?\PDO $sqlReader = null;

    #[Override]
    protected function __construct() {
        if(Env::getInstance()->has('db.writer.dsn')
            && Env::getInstance()->has('db.writer.user')
            && Env::getInstance()->has('db.writer.pwd')
            && Env::getInstance()->has('db.reader.dsn')
            && Env::getInstance()->has('db.reader.user')
            && Env::getInstance()->has('db.reader.pwd')) {
            $this->sqlWriter = new PDO(
                Env::getInstance()->get('db.writer.dsn'),
                Env::getInstance()->get('db.writer.user'),
                Env::getInstance()->get('db.writer.pwd'));
            $this->sqlReader = new PDO(
                Env::getInstance()->get('db.writer.dsn'),
                Env::getInstance()->get('db.writer.user'),
                Env::getInstance()->get('db.writer.pwd'));
        } else {
            throw new \Exception('Unable to start : missing configuration');
        }
    }

    public function execWrite(string $query, array $args = []): \PDOStatement {
        $stmt = $this->sqlWriter->prepare($query);
        $stmt->execute($args);
        return $stmt;
    }

    public function execWriteAndForget(string $query, array $args = []): bool {
        $stmt = $this->sqlWriter->prepare($query);
        return $stmt->execute($args);
    }

    public function execRead(string $query, array $args = []): \PDOStatement {
        $stmt = $this->sqlReader->prepare($query);
        $stmt->execute($args);
        return $stmt;
    }

    public function qf(string $query, array $args = []): array {
        $r = [];
        try {
            $s = $this->execRead($query, $args);
            $r = $s->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\Throwable $e){}
        return $r;
    }

    public function ql(string $query, string $key, array $args = []): array {
        $r = [];
        $s = $this->execRead($query, $args);
        while($l = $s->fetch(\PDO::FETCH_ASSOC)) {
            if(array_key_exists($key, $l)) {
                $r[] = $l[$key];
            }
        }
        return $r;
    }

    public function qk(string $query, string $key, string $kval, array $args = []): array {
        $r = [];
        $s = $this->execRead($query, $args);
        while($l = $s->fetch(\PDO::FETCH_ASSOC)) {
            if(array_key_exists($key, $l) && array_key_exists($kval, $l)) {
                $r[$l[$key]] = $l[$kval];
            }
        }
        return $r;
    }

    public function qo(string $query, string $key, array $args = []): mixed {
        $r = null;
        $s = $this->execRead($query, $args);
        if($l = $s->fetch(\PDO::FETCH_ASSOC)) {
            if(array_key_exists($key, $l)) {
                $r = $l[$key];
            }
        }
        return $r;
    }
}
