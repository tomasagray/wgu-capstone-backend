<?php
namespace Capstone;


use PDOException;

require_once "data/model/Assessment.php";

class AssessmentDao
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    function load($assessment_id)
    {
        $sql = "SELECT * FROM assessments WHERE assessment_id = :assessment_id";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( ['assessment_id' => $assessment_id] );
            // Fetch result
            $result = $query->fetchObject('Capstone\Assessment');
            // Clear buffer
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::e("Error loading assessment data for ID: " . $assessment_id);
        }

        // Error / no data found
        return null;
    }

    function loadAll()
    {
        $assessments = array();
        $sql = "SELECT * FROM assessments";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute();
            // Fetch
            while ($result = $query->fetchObject('Capstone\Assessment')) {
                array_push($assessments, $result);
            }
            // Clear
            $query->nextRowset();

            return $assessments;

        } catch (PDOException $e) {
            Log::e(
                "Error getting data from DB:"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function loadAssessmentsForCourse($course_id)
    {
        global $_SQL_R;
        $assessments = array();

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_course_assessments']);
            // Execute
            $query->execute([ ':course_id' => $course_id ]);
            // Fetch
            while($result = $query->fetchObject('Capstone\Assessment')) {
                array_push($assessments, $result);
            }
            // Clear
            $query->nextRowset();

            return $assessments;

        } catch (PDOException $e) {
            Log::e(
                "Could not retrieve assessments for course: " . $course_id
                    ."\n\tMessage: " .$e->getMessage()
            );
        }

        return null;
    }

    function save(Assessment $assessment)
    {
        Log::i("Attempting to save assessment: " . $assessment);
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['add_assessment']);
            // Execute
            $query->execute( $assessment->as_pdo_array() );

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Error saving assessment data for assessment: " . $assessment
                ."\nMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function update(Assessment $assessment)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['update_assessment']);
            // Execute
            $query->execute( $assessment->as_pdo_array() );

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Error updating assessment!"
                        ."\n\tMessage: ". $e->getMessage()
            );
        }

        return false;
    }

    function delete($assessment_id)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['remove_assessment']);
            // Execute
            $query->execute( [':assessment_id' => $assessment_id] );

            if( $query->rowCount() == 1 ) {
                return true;
            }
        } catch (PDOException $e) {
            Log::e(
                "Could not delete assessment: {$assessment_id}"
                        ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }
}