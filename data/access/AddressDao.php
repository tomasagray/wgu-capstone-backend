<?php
namespace Capstone;

use PDOException;

require_once 'data/model/Address.php';

class AddressDao
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    function load($address_id)
    {
        $sql = "SELECT * FROM addresses WHERE address_id = :address_id";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( ['address_id' => $address_id] );
            // Fetch
            $result = $query->fetchObject('Capstone\Address');
            // Clear buffer
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::e(
                "Could not load data for address ID: " . $address_id
                ."\nMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function loadAll()
    {
        $sql = "SELECT * FROM addresses";
        $addresses = array();

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute();
            // Fetch result
            while( $result = $query->fetchObject('Capstone\Address')) {
                array_push($addresses, $result);
            }
            // Clear buffer
            $query->nextRowset();

            return $addresses;

        } catch (PDOException $e) {
            Log::e(
                "Error loading all address data from DB:"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        // Nothing found
        return null;
    }

    function save(Address $address)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare( $_SQL_W['add_user_address'] );
            // Execute
            $query->execute( $address->as_pdo_array() );

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Could not save address data to DB for address: " . $address
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function update(Address $address)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['update_user_address']);
            // Execute
            $query->execute( $address->as_pdo_array() );

            return true;
        } catch (PDOException $e) {
            Log::e(
                "Error updating address!"
                        ."\nMessage: " . $e->getMessage()
            );
        }

        return false;
    }
}