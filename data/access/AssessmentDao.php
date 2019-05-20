<?php
namespace Capstone;


use PDO;
use PDOException;

require_once "data/model/Assessment.php";

class AssessmentDao
{
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
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

    function save($assessment)
    {
        // TODO: Implement save() method.
        $sql = "INSERT INTO "
                ."assessments(assessment_id, title, type, start_date, end_date)"
                ." VALUES (:assessment_id, :title, :type, :start_date, :end_date)";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
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
}