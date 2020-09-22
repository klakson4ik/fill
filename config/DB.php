<?php

namespace config;

class DB
{

    public static function connector()
    {
		  require 'config/Config_DB.php';
        return  new \PDO($dsn, $dbLogin, $dbPassword, $opt);
    }
}


