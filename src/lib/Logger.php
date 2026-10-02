<?php

namespace App\Rainbow\lib;

class Logger {
    const LOGDIR = 'logs';
    const STDOUT = ':stdout:';

    protected static $instances = [];
    public static function getInstance(string $channel): static {
        if(!array_key_exists($channel, static::$instances)) {
            static::$instances[$channel] = new Logger($channel);
        }
        return static::$instances[$channel];
    }

    public static function instant(string $toWrite): static {
        return static::getInstance(Env::getInstance()->get('logger', static::STDOUT))->write($toWrite);
    }

    protected ?string $channel = null;
    protected function __construct(string $channel) {
        $this->channel = preg_replace('`[^a-zA-Z0-9_\-:\.]`u', '', $channel);
    }

    public function write(string $str): static {
        $tw = '['.date('Y-m-d H:i:s').'] '.$str.PHP_EOL;
        if(static::STDOUT == $this->channel) {
            echo $tw;
        } else {
            $this->writeInFile($str);
        }
        return $this;
    }

    public function getLogsDir(): string {
        $dn = __DIR__.'/../../'.static::LOGDIR;
        return $dn;
    }

    public function getLogsFile(): string {
        $fn = preg_replace('`[^a-zA-Z0-9_\-]`u', '', $this->channel).'-'.date('Y-m').'.log';
        return $this->getLogsDir().'/'.$fn;
    }

    protected function writeInFile(string $str): static {
        $fh = fopen($this->getLogsFile(), 'a+');
        if(false !== $fh) {
            fwrite($fh, $str);
            fclose($fh);
        }
        return $this;
    }
}
