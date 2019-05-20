<?php
namespace Capstone;

use PDOException;

require_once "data/model/Term.php";

class TermDao
{
    private $db;
    public function __construct() {
        $this->db = Database::getInstance();
    }

    function load($term_id)
    {
        $sql = "SELECT * FROM terms WHERE term_id = :term_id";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( [':term_id' => $term_id] );
            // Fetch
            $result = $query->fetchObject('Capstone\Term');
            // Clear buffer
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::e(
                "Error getting data for term ID: " . $term_id
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function loadAll()
    {
        $terms = array();
        $sql = "SELECT * FROM terms";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute();
            // Fetch results
            while($result = $query->fetchObject('Capstone\Term')) {
                Log::i("Retrieved term: " . $result);
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
        $sql = "INSERT INTO terms(term_id, title, start_date, end_date) "
                ."VALUES (:term_id, :title, :start_date, :end_date)";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( $term->as_pdo_array() );

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Error saving term data: " . $term
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }
}