<?php

namespace model;

class Timer
{
    private static $start = .0;

    static function start()
    {
        self::$start = microtime(true);
    }

    static function finish()
    {
        return round(microtime(true) - self::$start, 2);
    }
}
