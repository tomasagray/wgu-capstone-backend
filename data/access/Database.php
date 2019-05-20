<?php
namespace Capstone;


use PDO;

require_once "log/Log.php";


class Database
{
    // PDO connection vars
    private static $PDOhost 	= 'localhost';
    private static $PDOdb	 	= 'capstone';
    private static $PDOuser 	= 'capstone';
    private static $PDOpass 	= 'cappy';
    private static $PDOcharset = 'utf8';
    private static $PDOopt		=
        [
            PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE 	=> PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   	=> false
        ];
    private static $PDOdsn;

    // Singleton instance
    private static $INSTANCE;

    private static function construct()
    {
        // Construct DSN
        self::$PDOdsn =
            "mysql:host="
            .self::$PDOhost
            .";dbname="
            .self::$PDOdb
            .";charset="
            .self::$PDOcharset;

        // Create the DB connection object
        self::$INSTANCE =
            new PDO (
                self::$PDOdsn,
                self::$PDOuser,
                self::$PDOpass,
                self::$PDOopt
            );
    }

    /**
     * @return PDO
     */
    public static function getInstance() {
        if(self::$INSTANCE === null) {
            self::construct();
        }

        return self::$INSTANCE;
    }
}