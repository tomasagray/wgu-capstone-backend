<?php
namespace Capstone;


use PDO;
use PDOException;

require_once "data/model/Student.php";

class StudentDao
{
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    function load($student_id)
    {
        $sql = "SELECT * FROM students WHERE student_id = :student_id";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( [':student_id' => $student_id] );
            // Fetch result
            $result = $query->fetchObject('Capstone\Student');
            // Clear buffer
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::e(
                "Could not fetch data for student ID: " . $student_id
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function loadAll()
    {
        $students = array();
        $sql = "SELECT * FROM students";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute();
            // Fetch results
            while( $result = $query->fetchObject('Capstone\Student')) {
                array_push($students, $result);
            }
            //Clear buffer
            $query->nextRowset();

            return $students;

        } catch (PDOException $e) {
            Log::e(
                "Error fetching all student data"
                        ."\n\tMessage: " . $e->getMessage()
            );
        }
    }

    function save(Student $student)
    {
        $sql = "INSERT INTO "
               ."students(student_id, first_name, last_name, email, phone, address_id, image_id) "
               ."VALUES (:student_id, :first_name, :last_name, :email, :phone, :address_id, :image_id)";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( $student->as_pdo_array() );

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Error saving student data to DB for student: " . $student
                    ."\nMessage: " . $e->getMessage()
            );
        }

        return false;
    }
}