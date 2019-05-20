<?php
namespace Capstone;

use PDO;
use PDOException;

require_once "data/model/Faculty.php";
require_once "data/access/Database.php";

class FacultyDao
{
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    function load($faculty_id)
    {
        $sql = "SELECT * FROM faculty WHERE faculty_id = :faculty_id";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( [':faculty_id' => $faculty_id] );
            // Fetch result
            $result = $query->fetch(PDO::FETCH_CLASS);
            // Clear buffer
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::e(
                "Could not load data for faculty ID: " . $faculty_id
                ."\nMessage: " . $e->getMessage()
            );
        }

        // No result found
        return null;
    }

    function loadAll()
    {
        $faculty = array();
        $sql = "SELECT * FROM faculty";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute();
            // Fetch result
            while($result = $query->fetchObject('Capstone\Faculty')) {
                Log::i("Fetched data for all faculty: " . $result);
                array_push($faculty, $result);
            }
            // Clear buffer
            $query->nextRowset();

            return $faculty;

        } catch (PDOException $e) {
            Log::e(
                "Error fetching data for all faculty:"
                      ."\n\tMessage: " . $e->getMessage());
        }

        return null;
    }

    function save(Faculty $faculty)
    {
        // TODO: Implement save() method.
        $sql = "INSERT INTO "
                ."faculty (faculty_id, first_name, last_name, email, phone, address_id, image_id) "
                ."VALUES(:faculty_id, :first_name, :last_name, :email, :phone, :address_id, :image_id)";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( $faculty->as_pdo_array() );

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Error saving data for faculty: " . $faculty
                ."\nMessage: " . $e->getMessage()
            );
        }

        // Something went wrong
        return false;
    }
}