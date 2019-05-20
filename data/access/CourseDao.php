<?php
namespace Capstone;


use PDO;
use PDOException;

require_once "data/model/Course.php";
require_once "data/access/BaseDao.php";
require_once "data/access/Database.php";

class CourseDao
{
    private $db;

    public function __construct(PDO $db) {
        // Get DB reference
        $this->db = $db;
    }

    function load($course_id)
    {
        $sql = "SELECT * FROM courses WHERE course_id = :course_id";

        try {
            // Create SQL query
            $query = $this->db->prepare($sql);
            // Execute query
            $query->execute( [':course_id' => $course_id] );
            // Fetch result
            $result = $query->fetchObject('Capstone\Course');
            // Clear buffer
            $query->nextRowset();
            // Return result
            return $result;

        } catch(PDOException $e) {
            Log::e(
                "Could not fetch data from DB for course ID: " . $course_id
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        // If nothing found, return null
        return null;
    }

    function loadAll()
    {
        $courses = array();
        $sql = "SELECT * FROM courses";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute();
            // Get results
            while($result = $query->fetchObject('Capstone\Course'))  {
                Log::i("Fetched: " . $result);
                array_push($courses, $result);
            }
            // Clear buffer
            $query->nextRowset();

            return $courses;

        } catch (PDOException $e) {
            Log::e (
                "Error fetching all courses data"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function save(Course $course)
    {
        $sql = "INSERT INTO "
                    ."courses(course_id, title, course_number, "
                    ."credits, start_date, end_date, status) "
                ."VALUES("
                    .":course_id, :title, :course_number, "
                    .":credits, :start_date, :end_date, :status"
                .")";

        try {
            // Prepare query
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( $course->as_pdo_array() );
            return true;

        } catch(PDOException $e) {
            Log::e(
                "Could not add course to DB: " . $course->getCourseId()
                ."\nMessage: " . $e->getMessage()
            );
        }

        // Could not insert course
        return false;
    }

}