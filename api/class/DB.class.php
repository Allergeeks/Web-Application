<?php
if(preg_match('~(.*)(travis)(.*)~' , __DIR__)) {
    require_once __DIR__ . '/../config-sample.inc';
} else {
    require_once __DIR__ . '/../config.inc';
}

class DB {
    private static $_instance; //The single instance

    /*
      Get an instance of the Database
      @return Instance
     */
    public static function con() {
        if (!self::$_instance) {
            self::$_instance = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
            
            if (mysqli_connect_error()) {
                trigger_error("Failed to connect to MySQL: " . mysql_connect_error(), E_USER_ERROR);
            }
            
            self::$_instance->set_charset("utf8");
        }
        return self::$_instance;
    }
}
