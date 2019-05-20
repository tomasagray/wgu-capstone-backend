<?php
namespace Capstone;

// Log.php

class Log
{
    private static $log_root = "/var/www/html/capstone/api/log/";
    private static $log_file = "log.txt";

    public static function i($msg) {
        $ilog_file = "info_".self::$log_file;
        $log_msg = "(info): " . $msg . "\n";

        self::write_log($ilog_file, $log_msg);
    }

    public static function e($msg) {
        $elog_file = "error_" . self::$log_file;
        $log_msg = "[ERROR]: " . $msg . "\n";

        self::write_log($elog_file, $log_msg);
    }

    private static function write_log($log_file, $msg) {
        file_put_contents(
            self::$log_root . $log_file,
            date('Y-m-d H:i:s') . '  ' . $msg,
            FILE_APPEND
        );
    }
}
