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

use Exception;
use PDOException;

require_once "data/model/Term.php";

class TermDao
{
    private $db;
    public function __construct() {
        $this->db = Database::getInstance();
    }

    function loadStudentTerms($student_id)
    {
        global $_SQL_R;
        $terms = array();

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_student_terms']);
            // Execute
            $query->execute( [':student_id' => $student_id] );
            // Fetch results
            while($result = $query->fetchObject('Capstone\Term')) {
                array_push($terms, $result);
            }

            // Clear buffer
            $query->nextRowSet();

            return $terms;

        } catch (PDOException $e) {
            Log::e(
                "Could not load all term data"
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function save(Term $term)
    {
        global $_SQL_W;
        Log::i("Saving: " . json_encode($term));
        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['enroll_student_in_term']);
            // Execute
            $query->execute( $term->as_pdo_array() );

            return true;

        } catch (Exception $e) {
            Log::e(
                "Error saving term data: " . json_encode($term)
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function update(Term $term)
    {
        global $_SQL_W;

        try {
            Log::i("Attempting to update term: {$term->getTermId()}");
            // Prepare
            $query = $this->db->prepare($_SQL_W['update_term']);
            // Execute
            $query->execute([
                ':term_id' => $term->getTermId(),
                ':start_date' => $term->getStartDate(),
                ':end_date' => $term->getEndDate()
            ]);

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Could not update term!"
                        ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function delete($term_id) {
        Log::i("Deleting term: {$term_id}");
        global $_SQL_W;
        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['unenroll_student_from_term']);
            // Execute
            $query->execute( [':term_id' => $term_id] );
            $rows = $query->rowCount();
                Log::i("Rows: {$rows}");
            if($rows == 1)
                return true;
            else
                return false;

        } catch (PDOException $e) {
            Log::e(
                "Could not delete term!"
                    ."\n\tMessage: " . $e->getMessage()
            );

            return false;
        }
    }

    function assignCourse($term_id, $course_data)
    {
        global $_SQL_W;
        try {
            $start = date('Y-m-d', strtotime($course_data->start_date));
            $end = date('Y-m-d', strtotime($course_data->end_date));
            // Prepare
            $query = $this->db->prepare($_SQL_W['assoc_class_with_term']);
            //Execute
            $data = [
                ':term_id'  => $term_id,
                ':course_id' => $course_data->course_id,
                ':start_date' => $start,
                ':end_date' => $end,
                ':status' => $course_data->course_status
            ];
            Log::i("Associating course data: " . json_encode($data));
            $result = $query->execute($data);
            Log::i("Result: {$result}");
            return true;

        } catch (PDOException $e) {
            Log::e(
                "Error associating term: ". $term_id
                ." with course: " . $course_data->course_id
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function clearTermCourses($term_id)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['clear_term_courses']);
            // Execute
            $query->execute([':term_id' => $term_id]);

            if($query->rowCount() > 0 )
                return true;
        } catch (PDOException $e) {
            Log::e(
                "Could not clear term courses for term: {$term_id}"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function getCoursesForTerm($term_id)
    {
        global $_SQL_R;
        $courses = array();

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_term_courses']);
            // Execute
            $query->execute([':term_id' => $term_id]);
            // Fetch
            while($result = $query->fetch()) {
                Log::i("GOT: " . json_encode($result));
                array_push($courses, $result);
            }

            return $courses;

        } catch (PDOException $e) {
            Log::e(
                "Could not load courses for term: {$term_id}"
                    ."\n\tMessage: " . $e->getMessage()
            );

            return null;
        }
    }
}
