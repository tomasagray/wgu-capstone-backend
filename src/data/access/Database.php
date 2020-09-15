<?php
/*
 * Copyright (c) 2020 Tomás Gray
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

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
