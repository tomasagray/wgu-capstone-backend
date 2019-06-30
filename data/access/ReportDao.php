<?php
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