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

    public static function s($msg) {
        $slog_file = "sec_" . self::$log_file;
        $log_msg = "[SECURITY]: " . $msg . "\n";

        self::write_log($slog_file, $log_msg);
    }

    // Write log data to disk
    private static function write_log($log_file, $msg) {
        file_put_contents(
            self::$log_root . $log_file,
            date('Y-m-d H:i:s') . '  ' . $msg,
            FILE_APPEND
        );
    }
}
