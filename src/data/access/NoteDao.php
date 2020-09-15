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

require_once "data/model/Note.php";
require_once "data/access/BaseDao.php";
require_once "data/access/Database.php";

class NoteDao
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function load($note_id)
    {
        $sql = "SELECT * FROM notes WHERE note_id = :note_id";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( [':note_id' => $note_id] );
            // Fetch
            $result = $query->fetchObject('Capstone\Note');
            // Clear buffer
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::e(
                "Error loading data for note ID: " . $note_id
                ."\n\tMessage: " . $e->getMessage()
            );
        }
    }

    public function loadAll()
    {
        $notes = array();
        $sql = "SELECT * FROM notes";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute();
            // Fetch
            while( $result = $query->fetchObject('Capstone\Note')) {
                array_push($notes, $result);
            }
            // Clear
            $query->nextRowset();

            return $notes;

        } catch (PDOException $e) {
            Log::e(
                "Failed to load all note data"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }
    }

    public function save(Note $note)
    {
        $sql =
            "INSERT INTO notes (note_id, text, updateDate) "
            ."VALUES(:note_id, :text, :update_date)";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( $note->as_pdo_array() );
            return true;

        } catch (PDOException $e) {
            Log::e(
                "Failed to save note to database: ". $note
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    public function update(Note $note)
    {
        $sql = "UPDATE notes SET text = :text, updateDate = :updateDate WHERE note_id = :noteId";

        try {
            // Prepare
            $query = $this->db->prepare($sql);
            // Execute
            $query->execute( [
                ':noteId' => $note->getNoteId(),
                ':text' => $note->getText(),
                ':updateDate' => $note->getUpdateDate()->format(DATE_FORMAT)
            ]);

            // Return number of rows affected
            return $query->rowCount();

        } catch (PDOException $e) {
            Log::e("Error updating note: " . $note->getNoteId());
        }

        return 0;
    }
}
