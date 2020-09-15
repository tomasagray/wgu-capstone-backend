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

class DocumentDao
{
    // Document storage location, relative to app root
    const STORAGE_LOCATION = "storage/documents/";

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    function loadAll()
    {
        global $_SQL_R;
        $documents = array();

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_all_documents']);
            // Execute
            $query->execute();
            // Fetch result
            while ($result = $query->fetch()) {
                array_push($documents, $result);
            }
            // Clear buffer
            $query->nextRowset();

            return $documents;

        } catch (PDOException $e) {
            Log::e(
                "Error loading all documents!"
                . "\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function load($document_id)
    {
        global $_SQL_R;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_document']);
            // Execute
            $query->execute([':document_id' => $document_id]);
            // Get result
            $result = $query->fetch();
            // Clear buffer
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::e(
                "Could not load document for ID: {$document_id}"
                . "\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }


    function save($data)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['add_document']);
            // Execute
            $query->execute([
                ':document_id' => $data['document_id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':file_name' => $data['file_name'],
                ':file_type' => $data['file_type'],
                ':file_size' => $data['file_size']
            ]);

            if ($query->rowCount() == 1) {
                return true;
            }
        } catch (PDOException $e) {
            Log::e(
                "Could not save document data!"
                . "\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }


    /**
     * Deletes file from local filesystem and from database.
     * This operation is _non-reversible_.
     *
     * @param $document_id String The identifier of the document to be deleted.
     * @return bool Was delete operation successful?
     */
    function delete($document_id)
    {
        global $_SQL_W;

        try {
            // Lookup document data
            $data = $this->load($document_id);
            // Verify record found
            if($data == null)
                return false;

            // Delete file from filesystem
            $file_path = self::STORAGE_LOCATION
                            . $data['document_id'] . '-'
                            . $data['file_name'];
            $delete_success = unlink($file_path);
            // If delete failed, stop
            if(!($delete_success))
                return false;

            // Prepare
            $query = $this->db->prepare($_SQL_W['remove_document']);
            // Execute
            $query->execute( [':document_id' => $document_id] );

            if($query->rowCount() == 1) {
                return true;
            }
        } catch (PDOException $e) {
            Log::e(
                "PDO Error deleting document: {$document_id}!"
                    ."\n\tMessage: " . $e->getMessage()
            );
        } catch (Exception $exception) {
            Log::e(
                "Error deleting document;"
                    ."\n\tMessage: " . $exception->getMessage()
            );
        }

        return false;
    }
}
