<?php
namespace Capstone;


use DateTime;
use PDOException;

require_once "data/model/Course.php";
require_once "data/access/BaseDao.php";
require_once "data/access/Database.php";

class CourseDao
{
    private $db;

    public function __construct() {
        // Get DB reference
        $this->db = Database::getInstance();
    }

    function load($course_id)
    {
        global $_SQL_R;
        try {
            // Create SQL query
            $query = $this->db->prepare($_SQL_R['get_course']);
            // Execute query
            $query->execute( [':course_id' => $course_id] );
            // Fetch result
            $result = $query->fetch();
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
        global $_SQL_R;
        $courses = array();

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_all_courses']);
            // Execute
            $query->execute();
            // Get results
            while($result = $query->fetchObject('Capstone\Course'))  {
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

    function loadCoursesForMentor($mentor_id)
    {
        global $_SQL_R;
        $courses = array();

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_mentor_courses']);
            // Execute
            $query->execute( [':mentor_id' => $mentor_id] );
            // Fetch
            while( $result = $query->fetch() ) {
                Log::i("fetched: " . json_encode($result));
                array_push($courses, $result);
            }
            // Clear buffer
            $query->nextRowset();

            return $courses;

        } catch (PDOException $e) {
            Log::e(
                "Error loading courses for mentor: {$mentor_id}"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function save(Course $course)
    {
        global $_SQL_W;
        try {
            // Prepare query
            Log::i("Adding course to DB: " . json_encode($course));
            $query = $this->db->prepare($_SQL_W['add_course']);
            // Execute
            $query->execute( $course->as_pdo_array() );

            // If mentor data is set
            if( $course->getMentor() != null ) {
                $this->assignMentor(
                    $course->getCourseId(),
                    $course->getMentor()->user_id
                );
            }

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

    function update(Course $course)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['update_course']);
            Log::i("Updating course with data: " . json_encode($course));
            // Execute
            $query->execute( $course->as_pdo_array() );

            // Update mentor data, if present
            if( $course->getMentor() != null) {
                $this->assignMentor(
                    $course->getCourseId(),
                    $course->getMentor()->user_id
                );
            }

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Error updating course data!"
                        ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function delete($course_id)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['remove_course']);
            // Execute
            $query->execute([':course_id' => $course_id]);

            if($query->rowCount() == 1)
                return true;
        } catch (PDOException $e) {
            Log::e(
                "Error deleting course for ID: {$course_id}"
                        ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function assignAssessment(Course $course, Assessment $assessment, DateTime $start, DateTime $end)
    {
        global $_SQL_W;
        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['ass']);
            // Execute
            $query->execute([
                ':course_id' => $course->getCourseId(),
                ':assessment_id' => $assessment->getAssessmentId(),
                ':start_date' => $start->format(DATE_FORMAT),
                ':end_date' => $end->format(DATE_FORMAT)
            ]);

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Could not assign assessment "
                . $assessment->getAssessmentId()
                . " to course " . $course->getCourseId()
            );
        }

        return false;
    }

    function assignMentor($course_id, $mentor_id)
    {
        global $_SQL_W;

        Log::i("Setting mentor for course: {$course_id} to {$mentor_id}");

        try {
            // Clear previous entry, if any
            $query = $this->db->prepare( $_SQL_W['remove_course_mentors']);
            $query->execute( [':course_id' => $course_id] );
            // Clear buffer
            $query->nextRowset();

            // Prepare
            $query = $this->db->prepare($_SQL_W['assign_mentor']);
            // Execute
            $query->execute([
                ':mentor_id'   => $mentor_id,
                ':course_id'    => $course_id
            ]);

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Could not save association for course: " . $course_id
                .", mentor: " . $mentor_id
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function clearMentorCourses($mentor_id)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['clear_mentorships']);
            // Execute
            $query->execute( [':mentor_id' => $mentor_id] );

            if($query->rowCount() > 0)
                return true;
        } catch (PDOException $e) {
            Log::e(
                "Could not clear all mentorships for mentor: {$mentor_id}"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

}