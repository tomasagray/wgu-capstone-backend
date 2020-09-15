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

use PDOException;

class ReportDao
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    function getCourseStatusDistribution()
    {
        global $_SQL_R;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_course_status_report']);
            // Execute
            $query->execute();
            // Fetch result; only 1 row expected
            $result = $query->fetch();
            // Clear buffer
            $query->nextRowset();

            return $result;
        } catch (PDOException $e) {
            Log::e(
                "Could not fetch course status distribution report data!"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function getEnrollmentsPerMonth()
    {
        global $_SQL_R;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_enrollments_month_report']);
            // Execute
            $query->execute();
            // Fetch and parse results
            $result = $query->fetchAll();
            // Clear buffer
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::e(
                "Error loading data for term enrollment report!"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function getStudentsPerCourse()
    {
        global $_SQL_R;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_students_per_course_report']);
            // Execute
            $query->execute();
            // Fetch results
            $result = $query->fetchAll();
            // Clear buffer
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::e(
                "Could not fetch data for students per course report!"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }
}
