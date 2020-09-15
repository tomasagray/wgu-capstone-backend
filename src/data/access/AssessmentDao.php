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
